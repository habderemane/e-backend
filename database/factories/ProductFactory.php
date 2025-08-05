<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nom = fake()->words(rand(2, 4), true);
        $marques = ['Samsung', 'Apple', 'Sony', 'Nike', 'Adidas', 'Zara', 'H&M', 'IKEA', 'Philips', 'LG'];

        return [
            'nom' => ucwords($nom),
            'description' => fake()->paragraphs(rand(2, 4), true),
            'slug' => Str::slug($nom) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'prix' => fake()->randomFloat(2, 9.99, 999.99),
            'prix_promo' => fake()->optional(0.3)->randomFloat(2, 5.99, 899.99),
            'date_debut_promo' => fake()->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'date_fin_promo' => fake()->optional(0.3)->dateTimeBetween('now', '+2 months'),
            'stock' => fake()->numberBetween(0, 100),
            'category_id' => Category::inRandomOrder()->first()->id,
            'images' => [],
            'actif' => fake()->boolean(90), // 90% de chance d'être actif
            'poids' => fake()->randomFloat(2, 0.1, 50.0),
            'dimensions' => [
                'longueur' => fake()->numberBetween(5, 200),
                'largeur' => fake()->numberBetween(5, 200),
                'hauteur' => fake()->numberBetween(1, 100)
            ],
            'marque' => fake()->randomElement($marques),
            'sku' => strtoupper(fake()->unique()->bothify('???-####')),
            'meta_title' => ucwords($nom) . ' - Boutique en ligne',
            'meta_description' => fake()->sentence(20),
        ];
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $prixOriginal = $attributes['prix'];
            return [
                'prix_promo' => $prixOriginal * fake()->randomFloat(2, 0.5, 0.9),
                'date_debut_promo' => fake()->dateTimeBetween('-1 week', 'now'),
                'date_fin_promo' => fake()->dateTimeBetween('now', '+1 month'),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
