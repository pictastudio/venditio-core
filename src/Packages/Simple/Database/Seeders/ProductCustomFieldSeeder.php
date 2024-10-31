<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductCustomField;

class ProductCustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        ProductCustomField::factory()->count(10)->create();
    }
}
