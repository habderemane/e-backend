<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('categorie')->actifs();

        // Filtres
        if ($request->has('recherche')) {
            $query->where('nom', 'ILIKE', '%' . $request->recherche . '%');
        }

        if ($request->has('categorie')) {
            $query->where('category_id', $request->categorie);
        }

        if ($request->has('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->has('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Tri
        $tri = $request->get('tri', 'nom');
        $ordre = $request->get('ordre', 'asc');
        $query->orderBy($tri, $ordre);

        // Pagination
        $perPage = $request->get('per_page', 12);
        $produits = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($produits->items()),
            'pagination' => [
                'current_page' => $produits->currentPage(),
                'last_page' => $produits->lastPage(),
                'per_page' => $produits->perPage(),
                'total' => $produits->total(),
            ]
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $produit = Product::with('categorie')->find($id);

        if (!$produit || !$produit->actif) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductResource($produit)
        ]);
    }

    // Autres méthodes (store, update, destroy) pour les admins...
}
