<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'identifier' => fake()->unique()->randomNumber(),
            'status' => fake()->randomElement(['abandoned', 'active', 'completed', 'failed', 'pending']),
            'sub_total_taxable' => fake()->randomFloat(2, 0, 100),
            'sub_total_tax' => fake()->randomFloat(2, 0, 10),
            'sub_total' => fake()->randomFloat(2, 0, 100),
            'shipping_fee' => fake()->randomFloat(2, 0, 20),
            'payment_fee' => fake()->randomFloat(2, 0, 10),
            'discount_code' => fake()->randomElement(['DISCOUNT10', 'SALE20']),
            'discount_amount' => fake()->randomFloat(2, 0, 50),
            'total_final' => fake()->randomFloat(2, 0, 200),
            'user_first_name' => fake()->firstName(),
            'user_last_name' => fake()->lastName(),
            'user_email' => fake()->safeEmail(),
            'addresses' => json_encode([
                'billing' => [],
                'shipping' => [],
            ]),
            'notes' => fake()->sentences(asText: true),
        ];
    }
}
