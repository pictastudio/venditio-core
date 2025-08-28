<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Enums\ProductStatus;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;

class ProductItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'product_id' => Product::factory(),
            'name' => fake()->word(),
            'slug' => fake()->slug(),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
            'active' => fake()->boolean(),
            'sku' => fake()->unique()->isbn13(),
            'ean' => fake()->unique()->ean13(),
            'visible_from' => fake()->dateTimeBetween('-1 year', 'now'),
            'visible_until' => fake()->dateTimeBetween('now', '+1 year'),
            'description' => fake()->paragraph(),
            'description_short' => fake()->sentence(),
            'images' => [
                [
                    'alt' => fake()->sentence(),
                    'src' => fake()->imageUrl(),
                ],
                [
                    'alt' => fake()->sentence(),
                    'src' => fake()->imageUrl(),
                ],
            ],
            'files' => [
                [
                    'name' => fake()->word(),
                    'src' => fake()->url(),
                ],
                [
                    'name' => fake()->word(),
                    'src' => fake()->url(),
                ],
            ],
            'length' => fake()->randomNumber(3),
            'width' => fake()->randomNumber(3),
            'height' => fake()->randomNumber(3),
            'weight' => fake()->randomNumber(3),
            'metadata' => ['keywords' => fake()->words(5)],
        ];
    }
}
