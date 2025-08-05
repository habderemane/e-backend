<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $produits = [
            // Électronique
            [
                'nom' => 'iPhone 15 Pro',
                'description' => 'Le dernier smartphone Apple avec puce A17 Pro, appareil photo professionnel et écran Super Retina XDR.',
                'prix' => 1229.00,
                'stock' => 25,
                'marque' => 'Apple',
                'sku' => 'IPHONE15PRO-128',
                'category_name' => 'Smartphones'
            ],
            [
                'nom' => 'MacBook Air M2',
                'description' => 'Ordinateur portable ultra-fin avec puce M2, écran Liquid Retina 13,6 pouces et autonomie exceptionnelle.',
                'prix' => 1499.00,
                'stock' => 15,
                'marque' => 'Apple',
                'sku' => 'MBA-M2-256',
                'category_name' => 'Ordinateurs'
            ],
            [
                'nom' => 'Samsung Galaxy S24',
                'description' => 'Smartphone Android haut de gamme avec IA intégrée, appareil photo 200MP et écran Dynamic AMOLED.',
                'prix' => 899.00,
                'stock' => 30,
                'marque' => 'Samsung',
                'sku' => 'SGS24-256',
                'category_name' => 'Smartphones'
            ],

            // Vêtements
            [
                'nom' => 'T-shirt Premium Coton Bio',
                'description' => 'T-shirt en coton biologique, coupe moderne et confortable. Disponible en plusieurs couleurs.',
                'prix' => 29.99,
                'stock' => 100,
                'marque' => 'EcoWear',
                'sku' => 'TSHIRT-BIO-M',
                'category_name' => 'Homme'
            ],
            [
                'nom' => 'Robe d\'été Florale',
                'description' => 'Robe légère et élégante avec motifs floraux, parfaite pour l\'été. Tissu respirant et coupe flatteuse.',
                'prix' => 79.99,
                'stock' => 50,
                'marque' => 'SummerStyle',
                'sku' => 'ROBE-FLORAL-M',
                'category_name' => 'Femme'
            ],

            // Maison & Jardin
            [
                'nom' => 'Aspirateur Robot Intelligent',
                'description' => 'Aspirateur robot avec navigation laser, contrôle par application et vidange automatique.',
                'prix' => 399.00,
                'stock' => 20,
                'marque' => 'CleanBot',
                'sku' => 'ROBOT-ASPIR-V2',
                'category_name' => 'Maison & Jardin'
            ],
            [
                'nom' => 'Set de Jardinage Professionnel',
                'description' => 'Kit complet d\'outils de jardinage en acier inoxydable avec étui de transport.',
                'prix' => 89.99,
                'stock' => 35,
                'marque' => 'GardenPro',
                'sku' => 'GARDEN-SET-PRO',
                'category_name' => 'Maison & Jardin'
            ],

            // Sports & Loisirs
            [
                'nom' => 'Vélo Électrique Urbain',
                'description' => 'Vélo électrique avec batterie longue durée, parfait pour les trajets urbains. Autonomie 80km.',
                'prix' => 1299.00,
                'stock' => 10,
                'marque' => 'UrbanBike',
                'sku' => 'EBIKE-URBAN-L',
                'category_name' => 'Sports & Loisirs'
            ],
            [
                'nom' => 'Tapis de Yoga Premium',
                'description' => 'Tapis de yoga antidérapant en caoutchouc naturel, épaisseur 6mm, avec sac de transport.',
                'prix' => 49.99,
                'stock' => 75,
                'marque' => 'ZenMat',
                'sku' => 'YOGA-MAT-6MM',
                'category_name' => 'Sports & Loisirs'
            ],

            // Livres & Médias
            [
                'nom' => 'Guide Complet du Développement Web',
                'description' => 'Livre technique couvrant HTML, CSS, JavaScript, React et les frameworks modernes.',
                'prix' => 45.00,
                'stock' => 60,
                'marque' => 'TechBooks',
                'sku' => 'BOOK-WEBDEV-2024',
                'category_name' => 'Livres & Médias'
            ]
        ];

        foreach ($produits as $produitData) {
            // Trouver la catégorie
            $category = $categories->where('nom', $produitData['category_name'])->first();
            if (!$category) {
                $category = $categories->where('nom', 'Électronique')->first();
            }

            Product::create([
                'nom' => $produitData['nom'],
                'description' => $produitData['description'],
                'slug' => Str::slug($produitData['nom']),
                'prix' => $produitData['prix'],
                'stock' => $produitData['stock'],
                'category_id' => $category->id,
                'marque' => $produitData['marque'],
                'sku' => $produitData['sku'],
                'actif' => true,
                'images' => [], // Sera rempli plus tard avec de vraies images
                'meta_title' => $produitData['nom'] . ' - Boutique en ligne',
                'meta_description' => substr($produitData['description'], 0, 160),
                'poids' => rand(100, 5000) / 100, // Poids aléatoire entre 1 et 50kg
                'dimensions' => [
                    'longueur' => rand(10, 100),
                    'largeur' => rand(10, 100),
                    'hauteur' => rand(5, 50)
                ]
            ]);
        }

        // Créer des produits supplémentaires avec Factory
        Product::factory(40)->create();
    }
}
