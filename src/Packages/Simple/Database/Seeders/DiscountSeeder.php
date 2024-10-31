<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\Discount;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        Discount::factory()->count(10)->create();
    }
}
