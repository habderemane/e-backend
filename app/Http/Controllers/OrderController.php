<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $cart[$validated['product_id']] = [
            'quantity' => ($cart[$validated['product_id']]['quantity'] ?? 0) + $validated['quantity'],
        ];
        session()->put('cart', $cart);

        return response()->json(['message' => 'Added to cart']);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get();
        $cartItems = $products->map(function ($product) use ($cart) {
            return [
                'product' => $product,
                'quantity' => $cart[$product->id]['quantity'],
            ];
        });

        return response()->json($cartItems);
    }

    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);
        if ($validated['quantity'] == 0) {
            unset($cart[$validated['product_id']]);
        } else {
            $cart[$validated['product_id']] = ['quantity' => $validated['quantity']];
        }
        session()->put('cart', $cart);

        return response()->json(['message' => 'Cart updated']);
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return response()->json(['message' => 'Item removed']);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'payment_mode' => 'required|in:pre-delivery,post-delivery',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * $cart[$product->id]['quantity'];
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => 'pending',
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => $validated['payment_mode'] == 'pre-delivery' ? 'paid' : 'pending',
            'shipping_address' => $validated['shipping_address'],
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $cart[$product->id]['quantity'],
                'price' => $product->price,
            ]);
        }

        session()->forget('cart');
        Mail::to(Auth::user()->email)->send(new OrderConfirmation($order));

        return response()->json($order, 201);
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('items.product')->get();
        return response()->json($orders);
    }

    public function adminIndex()
    {
        $orders = Order::with('items.product', 'user')->paginate(10);
        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:pending,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);
        Mail::to($order->user->email)->send(new OrderConfirmation($order));

        return response()->json($order);
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid',
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);
        Mail::to($order->user->email)->send(new OrderConfirmation($order));

        return response()->json($order);
    }

    public function stats()
    {
        $stats = [
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'order_count' => Order::count(),
            'top_products' => OrderItem::groupBy('product_id')
                ->selectRaw('product_id, SUM(quantity) as total_quantity')
                ->orderByDesc('total_quantity')
                ->take(5)
                ->with('product')
                ->get(),
            'payment_status' => [
                'paid' => Order::where('payment_status', 'paid')->count(),
                'pending' => Order::where('payment_status', 'pending')->count(),
            ],
        ];

        return response()->json($stats);
    }

    public function generateInvoice($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);
        $pdf = Pdf::loadView('invoices.order', ['order' => $order]);
        return $pdf->download('invoice-' . $order->id . '.pdf');
    }
}
