<?php

namespace PictaStudio\Venditio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\Venditio\Models\{ProductType, ProductVariant};

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
