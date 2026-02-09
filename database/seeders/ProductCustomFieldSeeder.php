<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\ProductCustomField;

class ProductCustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        ProductCustomField::factory()->count(10)->create();
    }
}
