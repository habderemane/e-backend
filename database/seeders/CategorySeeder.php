<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom' => 'Électronique',
                'description' => 'Appareils électroniques et gadgets technologiques',
                'slug' => 'electronique',
                'actif' => true,
                'ordre' => 1,
                'meta_title' => 'Électronique - Boutique en ligne',
                'meta_description' => 'Découvrez notre sélection d\'appareils électroniques'
            ],
            [
                'nom' => 'Vêtements',
                'description' => 'Mode et vêtements pour homme et femme',
                'slug' => 'vetements',
                'actif' => true,
                'ordre' => 2,
                'meta_title' => 'Vêtements - Mode en ligne',
                'meta_description' => 'Collection de vêtements tendance'
            ],
            [
                'nom' => 'Maison & Jardin',
                'description' => 'Articles pour la maison et le jardinage',
                'slug' => 'maison-jardin',
                'actif' => true,
                'ordre' => 3,
                'meta_title' => 'Maison & Jardin - Décoration',
                'meta_description' => 'Tout pour votre maison et votre jardin'
            ],
            [
                'nom' => 'Sports & Loisirs',
                'description' => 'Équipements sportifs et articles de loisirs',
                'slug' => 'sports-loisirs',
                'actif' => true,
                'ordre' => 4,
                'meta_title' => 'Sports & Loisirs - Équipements',
                'meta_description' => 'Matériel de sport et loisirs'
            ],
            [
                'nom' => 'Livres & Médias',
                'description' => 'Livres, films, musique et médias',
                'slug' => 'livres-medias',
                'actif' => true,
                'ordre' => 5,
                'meta_title' => 'Livres & Médias - Culture',
                'meta_description' => 'Livres, films et contenus culturels'
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            // Créer des sous-catégories pour certaines catégories
            if ($category->nom === 'Électronique') {
                Category::create([
                    'nom' => 'Smartphones',
                    'description' => 'Téléphones intelligents et accessoires',
                    'slug' => 'smartphones',
                    'parent_id' => $category->id,
                    'actif' => true,
                    'ordre' => 1
                ]);

                Category::create([
                    'nom' => 'Ordinateurs',
                    'description' => 'Ordinateurs portables et de bureau',
                    'slug' => 'ordinateurs',
                    'parent_id' => $category->id,
                    'actif' => true,
                    'ordre' => 2
                ]);
            }

            if ($category->nom === 'Vêtements') {
                Category::create([
                    'nom' => 'Homme',
                    'description' => 'Vêtements pour homme',
                    'slug' => 'vetements-homme',
                    'parent_id' => $category->id,
                    'actif' => true,
                    'ordre' => 1
                ]);

                Category::create([
                    'nom' => 'Femme',
                    'description' => 'Vêtements pour femme',
                    'slug' => 'vetements-femme',
                    'parent_id' => $category->id,
                    'actif' => true,
                    'ordre' => 2
                ]);
            }
        }
    }
}
