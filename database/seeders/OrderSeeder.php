<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = User::where('role', 'client')->get();
        $produits = Product::actifs()->get();

        // Créer 20 commandes d'exemple
        for ($i = 0; $i < 20; $i++) {
            $client = $clients->random();

            $commande = Order::create([
                'numero_commande' => Order::genererNumeroCommande(),
                'user_id' => $client->id,
                'statut' => collect(['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree'])->random(),
                'statut_paiement' => collect(['en_attente', 'paye'])->random(),
                'mode_paiement' => collect(['avant_livraison', 'apres_livraison'])->random(),
                'sous_total' => 0,
                'frais_livraison' => rand(0, 1) ? 9.99 : 0,
                'taxes' => 0,
                'remise' => 0,
                'total' => 0,
                'nom_livraison' => $client->nom,
                'prenom_livraison' => $client->prenom,
                'adresse_livraison' => $client->adresse ?? '123 Rue de la Livraison',
                'ville_livraison' => $client->ville ?? 'Paris',
                'code_postal_livraison' => $client->code_postal ?? '75001',
                'pays_livraison' => $client->pays ?? 'France',
                'telephone_livraison' => $client->telephone,
                'notes_client' => rand(0, 1) ? 'Livraison en point relais' : null,
                'created_at' => now()->subDays(rand(1, 30))
            ]);

            // Ajouter 1 à 4 articles par commande
            $nombreArticles = rand(1, 4);
            $sousTotal = 0;

            for ($j = 0; $j < $nombreArticles; $j++) {
                $produit = $produits->random();
                $quantite = rand(1, 3);
                $prixUnitaire = $produit->prix_final;

                OrderItem::create([
                    'order_id' => $commande->id,
                    'product_id' => $produit->id,
                    'nom_produit' => $produit->nom,
                    'description_produit' => $produit->description,
                    'prix_unitaire' => $prixUnitaire,
                    'quantite' => $quantite,
                    'total_ligne' => $prixUnitaire * $quantite,
                    'sku_produit' => $produit->sku,
                    'image_produit' => $produit->images ? [$produit->images[0]] : null
                ]);

                $sousTotal += $prixUnitaire * $quantite;
            }

            // Mettre à jour les totaux de la commande
            $commande->sous_total = $sousTotal;
            $commande->total = $sousTotal + $commande->frais_livraison + $commande->taxes - $commande->remise;
            $commande->save();
        }

        $this->command->info('Commandes créées avec succès !');
    }
}
