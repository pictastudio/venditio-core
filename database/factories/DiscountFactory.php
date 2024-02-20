<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['percentage', 'fixed']),
            'value' => fake()->numberBetween(1, 100),
            'name' => fake()->word(),
            'active' => fake()->boolean(),
            'starts_at' => fake()->dateTime(),
            'ends_at' => fake()->dateTime(),
        ];
    }
}
