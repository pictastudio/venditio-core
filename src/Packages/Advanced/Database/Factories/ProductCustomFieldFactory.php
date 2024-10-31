<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;

class ProductCustomFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'product_type_id' => ProductType::factory(),
            'name' => fake()->word(),
            'required' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(1, 100),
            'type' => fake()->randomElement(['text', 'number', 'date', 'boolean', 'select']),
            'options' => fake()->randomElement([null, json_encode(['option1', 'option2', 'option3'])]),
        ];
    }
}
