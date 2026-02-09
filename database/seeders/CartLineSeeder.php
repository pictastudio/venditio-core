<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\CartLine;

class CartLineSeeder extends Seeder
{
    public function run(): void
    {
        CartLine::factory()->count(10)->create();
    }
}
