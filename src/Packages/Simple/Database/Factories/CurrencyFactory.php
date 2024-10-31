<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Models\Currency;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;
    public function definition(): array
    {
        $code = fake()->currencyCode();

        return [
            'name' => $code,
            'code' => $code,
            'symbol' => null,
            'exchange_rate' => fake()->randomFloat(4, 0, 100),
            'decimal_places' => fake()->numberBetween(0, 4),
            'is_enabled' => fake()->boolean(),
            'is_default' => fake()->boolean(),
        ];
    }
}
