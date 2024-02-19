<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaxClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Standard',
        ];
    }
}
