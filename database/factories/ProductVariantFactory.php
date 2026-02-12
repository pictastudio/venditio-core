<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Models\{ProductType, ProductVariant};

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'product_type_id' => ProductType::factory(),
            'name' => fake()->word(),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
