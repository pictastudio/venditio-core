<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::factory()->count(10)->create();
    }
}
