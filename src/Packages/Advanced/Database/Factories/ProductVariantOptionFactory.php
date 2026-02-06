<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariant;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariantOption;

class ProductVariantOptionFactory extends Factory
{
    protected $model = ProductVariantOption::class;

    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'value' => fake()->randomElement(['option1', 'option2', 'option3']),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
