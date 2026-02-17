<?php

namespace PictaStudio\Venditio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\Venditio\Models\Municipality;

class MunicipalityFactory extends Factory
{
    protected $model = Municipality::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'country_zone' => fake()->word(),
            'zip' => fake()->postcode(),
            'phone_prefix' => fake()->numerify('0##'),
            'istat_code' => fake()->numerify('######'),
            'cadastral_code' => fake()->bothify('A###'),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
}
