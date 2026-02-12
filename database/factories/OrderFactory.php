<?php

namespace PictaStudio\Venditio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\Venditio\Models\Order;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'identifier' => fake()->unique()->randomNumber(),
            'status' => fake()->randomElement(['pending', 'processing', 'completed']),
            'tracking_code' => fake()->randomNumber(),
            'tracking_link' => fake()->url(),
            'last_tracked_at' => fake()->dateTime(),
            'courier_code' => fake()->randomElement(['UPS', 'FedEx', 'DHL']),
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
            'customer_notes' => fake()->sentences(asText: true),
            'admin_notes' => fake()->sentences(asText: true),
            'approved_at' => fake()->dateTime(),
        ];
    }
}
