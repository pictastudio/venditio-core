<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PictaStudio\VenditioCore\Packages\Simple\Enums\AddressType;
use PictaStudio\VenditioCore\Packages\Simple\Models\Address;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(config('venditio-core.addresses.type_enum')::cases())->value,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'sex' => fake()->randomElement(['m', 'f']),
            'phone' => fake()->phoneNumber(),
            'vat_number' => fake()->randomNumber(11),
            'fiscal_code' => fake()->randomNumber(16),
            'company_name' => fake()->company(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip' => fake()->postcode(),
            'country' => fake()->country(),
            'birth_date' => fake()->date(),
            'birth_place' => fake()->city(),
            'notes' => fake()->sentences(asText: true),
        ];
    }
}
