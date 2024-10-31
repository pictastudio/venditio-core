<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariant;

class ProductVariantOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'product_variant_id' => ProductVariant::factory(),
            'value' => fake()->randomElement(['option1', 'option2', 'option3']),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
