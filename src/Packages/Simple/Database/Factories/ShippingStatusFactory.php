<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Models\ShippingStatus;

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
