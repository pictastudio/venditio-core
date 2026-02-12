<?php

namespace PictaStudio\Venditio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\Venditio\Models\ShippingStatus;

class ShippingStatusFactory extends Factory
{
    protected $model = ShippingStatus::class;

    public function definition(): array
    {
        return [
            'external_code' => fake()->regexify('[A-Za-z0-9]{10}'),
            'name' => fake()->name(),
        ];
    }
}
