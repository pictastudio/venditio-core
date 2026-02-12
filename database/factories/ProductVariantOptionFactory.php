<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Models\{ProductVariant, ProductVariantOption};

class ProductVariantOptionFactory extends Factory
{
    protected $model = ProductVariantOption::class;

    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'name' => fake()->randomElement(['option1', 'option2', 'option3']),
            'image' => null,
            'hex_color' => null,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
