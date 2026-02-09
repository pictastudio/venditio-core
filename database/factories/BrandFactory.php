<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
