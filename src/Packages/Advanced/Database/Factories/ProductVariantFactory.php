<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
