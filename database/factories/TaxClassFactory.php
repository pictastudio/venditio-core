<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Models\TaxClass;

class TaxClassFactory extends Factory
{
    protected $model = TaxClass::class;

    public function definition(): array
    {
        return [
            'name' => 'Standard',
        ];
    }
}
