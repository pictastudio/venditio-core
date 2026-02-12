<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\ProductCustomField;

class ProductCustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        ProductCustomField::factory()->count(10)->create();
    }
}
