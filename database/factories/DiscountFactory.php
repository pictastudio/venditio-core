<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Enums\DiscountType;
use PictaStudio\VenditioCore\Models\Discount;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(DiscountType::cases())->value,
            'value' => fake()->numberBetween(1, 100),
            'name' => fake()->word(),
            'active' => fake()->boolean(),
            'starts_at' => fake()->dateTime(),
            'ends_at' => fake()->dateTime(),
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
