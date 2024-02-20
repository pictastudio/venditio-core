<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Models\ProductVariant;

class ProductVariantOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'product_variant_id' => ProductVariant::factory(),
            'value' => fake()->randomElement(['option1', 'option2', 'option3']),
            'order' => fake()->numberBetween(1, 100),
        ];
    }
}
