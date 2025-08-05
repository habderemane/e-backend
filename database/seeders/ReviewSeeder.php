<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenir des utilisateurs clients et des produits
        $clients = User::where('role', 'client')->get();
        $produits = Product::actifs()->get();

        // Créer des avis réalistes
        $avisExemples = [
            [
                'note' => 5,
                'titre' => 'Excellent produit !',
                'commentaire' => 'Je suis très satisfait de cet achat. La qualité est au rendez-vous et la livraison a été rapide.',
                'recommande' => true
            ],
            [
                'note' => 4,
                'titre' => 'Très bon rapport qualité-prix',
                'commentaire' => 'Produit conforme à mes attentes. Quelques petits défauts mais rien de rédhibitoire.',
                'recommande' => true
            ],
            [
                'note' => 5,
                'titre' => 'Je recommande vivement',
                'commentaire' => 'Parfait ! Exactement ce que je cherchais. Je recommande sans hésitation.',
                'recommande' => true
            ],
            [
                'note' => 3,
                'titre' => 'Correct sans plus',
                'commentaire' => 'Le produit fait le travail mais sans plus. La qualité pourrait être meilleure.',
                'recommande' => false
            ],
            [
                'note' => 4,
                'titre' => 'Bon achat',
                'commentaire' => 'Satisfait de mon achat. Le produit correspond à la description.',
                'recommande' => true
            ],
            [
                'note' => 5,
                'titre' => 'Parfait !',
                'commentaire' => 'Rien à redire, c\'est exactement ce que j\'attendais. Livraison rapide en plus.',
                'recommande' => true
            ],
            [
                'note' => 2,
                'titre' => 'Déçu de cet achat',
                'commentaire' => 'Le produit ne correspond pas vraiment à mes attentes. La qualité n\'est pas au rendez-vous.',
                'recommande' => false
            ],
            [
                'note' => 4,
                'titre' => 'Bonne qualité',
                'commentaire' => 'Produit de bonne qualité, je suis globalement satisfait de mon achat.',
                'recommande' => true
            ]
        ];

        // Créer des avis pour chaque produit
        foreach ($produits as $produit) {
            // Nombre aléatoire d'avis par produit (0 à 5)
            $nombreAvis = rand(0, 5);

            for ($i = 0; $i < $nombreAvis; $i++) {
                $client = $clients->random();
                $avisTemplate = $avisExemples[array_rand($avisExemples)];

                // Vérifier si ce client n'a pas déjà laissé un avis pour ce produit
                $avisExistant = Review::where('user_id', $client->id)
                    ->where('product_id', $produit->id)
                    ->exists();

                if (!$avisExistant) {
                    // Trouver une commande livrée pour ce client et ce produit (pour vérification)
                    $commande = OrderItem::whereHas('commande', function ($query) use ($client) {
                        $query->where('user_id', $client->id)
                            ->where('statut', 'livree');
                    })->where('product_id', $produit->id)
                        ->first()?->commande;

                    Review::create([
                        'user_id' => $client->id,
                        'product_id' => $produit->id,
                        'order_id' => $commande?->id,
                        'note' => $avisTemplate['note'],
                        'titre' => $avisTemplate['titre'],
                        'commentaire' => $avisTemplate['commentaire'],
                        'recommande' => $avisTemplate['recommande'],
                        'verifie' => !is_null($commande),
                        'modere' => true, // Pré-modéré pour les données de test
                        'actif' => true,
                        'utile_count' => rand(0, 10)
                    ]);
                }
            }
        }

        $this->command->info('Avis créés avec succès !');
    }
}
