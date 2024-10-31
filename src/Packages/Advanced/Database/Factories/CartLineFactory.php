<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CartLineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_name' => fake()->word(),
            'product_sku' => fake()->word(),
            'unit_price' => fake()->randomFloat(2, 0, 100),
            'unit_discount' => fake()->randomFloat(2, 0, 10),
            'unit_final_price' => fake()->randomFloat(2, 0, 100),
            'unit_final_price_tax' => fake()->randomFloat(2, 0, 100),
            'unit_final_price_taxable' => fake()->boolean(),
            'qty' => fake()->randomNumber(2),
            'total_final_price' => fake()->randomFloat(2, 0, 100),
            'tax_rate' => fake()->randomFloat(2, 0, 10),
            'product_item' => fake()->word(),
        ];
    }
}
