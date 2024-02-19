<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderLineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_sku' => fake()->word(),
            'product_name' => fake()->word(),
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
