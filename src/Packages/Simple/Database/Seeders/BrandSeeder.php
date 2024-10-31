<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::factory()->count(10)->create();
    }
}
