<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function creerCommande(User $user, array $donneesCommande, array $articlesCommande): Order
    {
        return DB::transaction(function () use ($user, $donneesCommande, $articlesCommande) {
            // Créer la commande
            $commande = Order::create([
                'numero_commande' => Order::genererNumeroCommande(),
                'user_id' => $user->id,
                'statut' => 'en_attente',
                'statut_paiement' => 'en_attente',
                'mode_paiement' => $donneesCommande['mode_paiement'] ?? 'avant_livraison',
                'sous_total' => 0,
                'total' => 0,
                'nom_livraison' => $donneesCommande['nom_livraison'],
                'prenom_livraison' => $donneesCommande['prenom_livraison'],
                'adresse_livraison' => $donneesCommande['adresse_livraison'],
                'ville_livraison' => $donneesCommande['ville_livraison'],
                'code_postal_livraison' => $donneesCommande['code_postal_livraison'],
                'pays_livraison' => $donneesCommande['pays_livraison'] ?? 'France',
            ]);

            // Ajouter les articles
            $sousTotal = 0;
            foreach ($articlesCommande as $articleData) {
                $produit = Product::findOrFail($articleData['product_id']);

                // Vérifier le stock
                if ($produit->stock < $articleData['quantite']) {
                    throw new \Exception("Stock insuffisant pour: {$produit->nom}");
                }

                // Créer l'article de commande
                $article = OrderItem::create([
                    'order_id' => $commande->id,
                    'product_id' => $produit->id,
                    'nom_produit' => $produit->nom,
                    'prix_unitaire' => $produit->prix_final,
                    'quantite' => $articleData['quantite'],
                    'sku_produit' => $produit->sku,
                ]);

                $sousTotal += $article->total_ligne;

                // Décrémenter le stock
                $produit->decrement('stock', $articleData['quantite']);
            }

            // Mettre à jour les totaux
            $commande->sous_total = $sousTotal;
            $commande->total = $sousTotal + ($donneesCommande['frais_livraison'] ?? 0);
            $commande->save();

            return $commande->load('articles.produit');
        });
    }
}
