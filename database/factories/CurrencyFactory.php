<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->currencyCode();

        return [
            'name' => $code,
            'code' => $code,
            'symbol' => null,
            'exchange_rate' => fake()->randomFloat(4, 0, 100),
            'decimal_places' => fake()->numberBetween(0, 4),
            'enabled' => fake()->boolean(),
            'default' => fake()->boolean(),
        ];
    }
}
