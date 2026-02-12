<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\CartLine;

class CartLineSeeder extends Seeder
{
    public function run(): void
    {
        CartLine::factory()->count(10)->create();
    }
}
