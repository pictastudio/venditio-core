<?php

namespace PictaStudio\Venditio\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\Venditio\Models\{Country, TaxClass};

class TaxClassSeeder extends Seeder
{
    public function run(): void
    {
        $taxClass = TaxClass::query()->create([
            'name' => 'Standard',
        ]);

        $taxClass->countries()->attach(Country::where('iso_2', 'IT')->value('id'), [
            'rate' => 22,
        ]);
    }
}
