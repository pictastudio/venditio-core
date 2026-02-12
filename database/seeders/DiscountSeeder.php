<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\Discount;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        Discount::factory()->count(10)->create();
    }
}
