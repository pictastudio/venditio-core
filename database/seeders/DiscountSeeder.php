<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Discount;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        Discount::factory()->count(10)->create();
    }
}
