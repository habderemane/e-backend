<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Http\Resources\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Liste des avis d'un produit
     */
    public function index(Request $request, string $productId): JsonResponse
    {
        $produit = Product::find($productId);

        if (!$produit) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        }

        $query = Review::with('utilisateur')
            ->where('product_id', $productId)
            ->actifs()
            ->moderes();

        // Filtrer par note
        if ($request->has('note') && $request->note) {
            $query->parNote($request->note);
        }

        // Tri
        $tri = $request->get('tri', 'recent');
        switch ($tri) {
            case 'utile':
                $query->parUtilite();
                break;
            case 'ancien':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->recents();
        }

        $avis = $query->paginate(10);
        $statistiques = Review::noteMoyenneProduit($productId);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($avis->items()),
            'pagination' => [
                'current_page' => $avis->currentPage(),
                'last_page' => $avis->lastPage(),
                'per_page' => $avis->perPage(),
                'total' => $avis->total(),
            ],
            'statistiques' => $statistiques
        ]);
    }

    /**
     * Créer un nouvel avis (utilisateur connecté)
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

        // Vérifier si l'utilisateur peut laisser un avis
        if (!Review::peutLaisserAvis($user->id, $productId)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez avoir acheté ce produit pour laisser un avis, ou vous avez déjà laissé un avis.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'note' => 'required|integer|min:1|max:5',
            'titre' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string|max:1000',
            'recommande' => 'boolean'
        ], [
            'note.required' => 'La note est obligatoire',
            'note.min' => 'La note doit être entre 1 et 5',
            'note.max' => 'La note doit être entre 1 et 5',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Trouver la commande correspondante pour vérification
            $commande = \App\Models\OrderItem::whereHas('commande', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('statut', 'livree');
            })->where('product_id', $productId)
                ->first()?->commande;

            $avis = Review::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'order_id' => $commande?->id,
                'note' => $request->note,
                'titre' => $request->titre,
                'commentaire' => $request->commentaire,
                'recommande' => $request->get('recommande', true),
                'verifie' => !is_null($commande),
                'modere' => false, // Nécessite modération
                'actif' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avis créé avec succès. Il sera visible après modération.',
                'data' => new ReviewResource($avis->load('utilisateur'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'avis'
            ], 500);
        }
    }

    /**
     * Marquer un avis comme utile
     */
    public function markAsHelpful(string $reviewId): JsonResponse
    {
        $avis = Review::find($reviewId);

        if (!$avis) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouvé'
            ], 404);
        }

        $avis->increment('utile_count');

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre retour !',
            'utile_count' => $avis->utile_count
        ]);
    }

    /**
     * Modérer un avis (Admin seulement)
     */
    public function moderate(Request $request, string $reviewId): JsonResponse
    {
        $avis = Review::find($reviewId);

        if (!$avis) {
            return response()->json([
                'success' => false,
                'message' => 'Avis non trouvé'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'modere' => 'required|boolean',
            'actif' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $avis->update([
            'modere' => $request->modere,
            'actif' => $request->get('actif', $avis->actif)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Avis modéré avec succès',
            'data' => new ReviewResource($avis->load('utilisateur'))
        ]);
    }
}
