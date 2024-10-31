<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Enums\ProductMeasuringUnit;
use PictaStudio\VenditioCore\Packages\Simple\Enums\ProductStatus;
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
            'brand_id' => Brand::factory(),
            // 'product_type_id' => ProductType::factory(),
            'tax_class_id' => TaxClass::factory(),
            'name' => fake()->word(),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
            'active' => fake()->boolean(),
            'new' => fake()->boolean(),
            'in_evidence' => fake()->boolean(),
            'sku' => fake()->unique()->isbn13(),
            'ean' => fake()->unique()->ean13(),
            'visible_from' => fake()->dateTimeBetween('-1 year', 'now'),
            'visible_until' => fake()->dateTimeBetween('now', '+1 year'),
            'description' => fake()->paragraph(),
            'description_short' => fake()->sentence(),
            'images' => [
                [
                    'alt' => fake()->sentence(),
                    'src' => fake()->imageUrl(),
                ],
                [
                    'alt' => fake()->sentence(),
                    'src' => fake()->imageUrl(),
                ],
            ],
            'files' => [
                [
                    'name' => fake()->word(),
                    'src' => fake()->url(),
                ],
                [
                    'name' => fake()->word(),
                    'src' => fake()->url(),
                ]
            ],
            'measuring_unit' => fake()->randomElement(ProductMeasuringUnit::cases())->value,
            'length' => fake()->randomNumber(3),
            'width' => fake()->randomNumber(3),
            'height' => fake()->randomNumber(3),
            'weight' => fake()->randomNumber(3),
            'metadata' => ['keywords' => fake()->words(5)],
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
            'status' => ProductStatus::Draft,
        ]);
    }

    public function published(): self
    {
        return $this->state([
            'status' => ProductStatus::Published,
        ]);
    }

    public function archived(): self
    {
        return $this->state([
            'status' => ProductStatus::Archived,
        ]);
    }
}
