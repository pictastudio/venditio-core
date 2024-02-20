<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'active' => fake()->boolean(),
        ];
    }
}
