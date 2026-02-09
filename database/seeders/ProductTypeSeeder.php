<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        ProductType::factory()->count(10)->create();
    }
}
