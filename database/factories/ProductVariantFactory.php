<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'order' => fake()->numberBetween(1, 100),
        ];
    }
}
