<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    /**
     * Obtenir la wishlist de l'utilisateur connecté
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $wishlistItems = Wishlist::with('produit.categorie')
            ->where('user_id', $user->id)
            ->get();

        $produits = $wishlistItems->map(function ($item) {
            return $item->produit;
        })->filter(); // Filtrer les produits supprimés

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($produits),
            'total' => $produits->count()
        ]);
    }

    /**
     * Ajouter un produit à la wishlist
     */
    public function store(Request $request, string $productId): JsonResponse
    {
        $user = auth()->user();
        $produit = Product::find($productId);

        if (!$produit) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        if (Wishlist::estDansWishlist($user->id, $productId)) {
            return response()->json([
                'success' => false,
                'message' => 'Ce produit est déjà dans votre liste de souhaits'
            ], 409);
        }

        $ajout = Wishlist::ajouterProduit($user->id, $productId);

        if ($ajout) {
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté à votre liste de souhaits',
                'data' => new ProductResource($produit->load('categorie'))
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'ajout à la liste de souhaits'
        ], 500);
    }

    /**
     * Retirer un produit de la wishlist
     */
    public function destroy(string $productId): JsonResponse
    {
        $user = auth()->user();

        $supprime = Wishlist::retirerProduit($user->id, $productId);

        if ($supprime) {
            return response()->json([
                'success' => true,
                'message' => 'Produit retiré de votre liste de souhaits'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produit non trouvé dans votre liste de souhaits'
        ], 404);
    }

    /**
     * Vérifier si un produit est dans la wishlist
     */
    public function check(string $productId): JsonResponse
    {
        $user = auth()->user();

        $estDansWishlist = Wishlist::estDansWishlist($user->id, $productId);

        return response()->json([
            'success' => true,
            'in_wishlist' => $estDansWishlist
        ]);
    }

    /**
     * Vider la wishlist
     */
    public function clear(): JsonResponse
    {
        $user = auth()->user();

        $supprime = Wishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Liste de souhaits vidée ({$supprime} produits supprimés)"
        ]);
    }
}
