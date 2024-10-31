<?php

namespace PictaStudio\VenditioCore\Packages\Advanced\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        ProductType::factory()->count(10)->create();
    }
}
