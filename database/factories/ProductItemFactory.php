<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Models\Product;

class ProductItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'product_id' => Product::factory(),
            'name' => fake()->word(),
            'slug' => fake()->slug(),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'active' => fake()->boolean(),
            'sku' => fake()->isbn13(),
            'ean' => fake()->ean13(),
            'visible_from' => fake()->dateTimeBetween('-1 year', 'now'),
            'visible_to' => fake()->dateTimeBetween('now', '+1 year'),
            'description' => fake()->paragraph(),
            'description_short' => fake()->sentence(),
            'images' => json_encode([fake()->imageUrl()]),
            'weight' => fake()->randomNumber(3),
            'length' => fake()->randomNumber(3),
            'width' => fake()->randomNumber(3),
            'depth' => fake()->randomNumber(3),
            'metadata' => json_encode(['keywords' => fake()->words(5)]),
        ];
    }
}
