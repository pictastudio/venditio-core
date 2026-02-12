<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::factory()->count(10)->create();
    }
}
