<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;

class ProductTypeFactory extends Factory
{
    protected $model = ProductType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'active' => fake()->boolean(),
        ];
    }
}
