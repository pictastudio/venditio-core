<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_code' => fake()->regexify('[A-Za-z0-9]{10}'),
            'name' => fake()->name(),
        ];
    }
}
