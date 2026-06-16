<?php

namespace PictaStudio\Venditio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\Venditio\Models\PaymentMethod;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'code' => mb_strtoupper(fake()->unique()->bothify('PAY-###')),
            'name' => fake()->randomElement(['Credit Card', 'Bank Transfer', 'PayPal']) . ' ' . fake()->numerify('###'),
            'active' => true,
            'flat_fee' => fake()->randomFloat(2, 0, 10),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
