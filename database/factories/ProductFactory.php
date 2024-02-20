<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Enums\ProductMeasuringUnit;
use PictaStudio\VenditioCore\Models\Brand;
use PictaStudio\VenditioCore\Models\ProductType;
use PictaStudio\VenditioCore\Models\TaxClass;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            // 'brand_id' => Brand::factory(),
            // 'product_type_id' => ProductType::factory(),
            // 'tax_class_id' => TaxClass::factory(),
            'name' => fake()->word(),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'active' => fake()->boolean(),
            'new' => fake()->boolean(),
            'in_evidence' => fake()->boolean(),
            'visible_from' => fake()->dateTimeBetween('-1 year', 'now'),
            'visible_to' => fake()->dateTimeBetween('now', '+1 year'),
            'description' => fake()->paragraph(),
            'description_short' => fake()->sentence(),
            'images' => json_encode([fake()->imageUrl()]),
            'measuring_unit' => fake()->randomElement(ProductMeasuringUnit::cases())->value,
            'weight' => fake()->randomNumber(3),
            'length' => fake()->randomNumber(3),
            'width' => fake()->randomNumber(3),
            'depth' => fake()->randomNumber(3),
            'metadata' => json_encode(['keywords' => fake()->words(5)]),
        ];
    }
}
