<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        ProductType::factory()->count(10)->create();
    }
}
