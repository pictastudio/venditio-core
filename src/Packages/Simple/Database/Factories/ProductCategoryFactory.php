<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'active' => true,
            'sort_order' => fake()->unique()->numberBetween(0, 1000),
        ];
    }
}
