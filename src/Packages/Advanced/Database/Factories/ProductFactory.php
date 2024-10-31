<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Enums\ProductMeasuringUnit;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductType;
use PictaStudio\VenditioCore\Packages\Simple\Models\TaxClass;

class ProductFactory extends Factory
{
    protected $model = Product::class;

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
            'visible_until' => fake()->dateTimeBetween('now', '+1 year'),
            'description' => fake()->paragraph(),
            'description_short' => fake()->sentence(),
            'images' => json_encode([fake()->imageUrl()]),
            'measuring_unit' => fake()->randomElement(ProductMeasuringUnit::cases())->value,
            'length' => fake()->randomNumber(3),
            'width' => fake()->randomNumber(3),
            'height' => fake()->randomNumber(3),
            'weight' => fake()->randomNumber(3),
            'metadata' => json_encode(['keywords' => fake()->words(5)]),
        ];
    }

    public function inactive(): self
    {
        return $this->state([
            'active' => false,
        ]);
    }

    public function draft(): self
    {
        return $this->state([
            'status' => 'draft',
        ]);
    }

    public function published(): self
    {
        return $this->state([
            'status' => 'published',
        ]);
    }

    public function archived(): self
    {
        return $this->state([
            'status' => 'archived',
        ]);
    }
}
