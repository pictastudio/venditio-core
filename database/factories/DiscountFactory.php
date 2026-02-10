<?php

namespace PictaStudio\VenditioCore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Enums\DiscountType;
use PictaStudio\VenditioCore\Models\{Discount, Product};

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'discountable_type' => 'product',
            'discountable_id' => Product::factory(),
            'type' => fake()->randomElement(DiscountType::cases())->value,
            'value' => fake()->numberBetween(1, 100),
            'name' => fake()->word(),
            'code' => mb_strtoupper(fake()->bothify('DISC-#####')),
            'active' => true,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDay(),
            'rules' => [],
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
