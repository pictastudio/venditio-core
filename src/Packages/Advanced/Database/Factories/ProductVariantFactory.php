<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariant;

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
