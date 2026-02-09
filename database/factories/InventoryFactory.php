<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Enums\DiscountType;
use PictaStudio\VenditioCore\Models\Discount;
use PictaStudio\VenditioCore\Models\Inventory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'stock' => fake()->numberBetween(1, 1000),
            'stock_min' => fake()->numberBetween(1, 1000),
            'price' => fake()->randomFloat(2, 1, 1000),
        ];
    }

    public function percentage(): self
    {
        return $this->state([
            'type' => DiscountType::Percentage,
        ]);
    }

    public function fixed(): self
    {
        return $this->state([
            'type' => DiscountType::Fixed,
        ]);
    }
}
