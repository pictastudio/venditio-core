<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Packages\Simple\Models\Country;
use PictaStudio\VenditioCore\Packages\Simple\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        // Currency::factory()
        //     ->for(Country::where('iso_2', 'IT')->first())
        //     ->create([
        //         'name' => 'Euro',
        //         'code' => 'EUR',
        //         'symbol' => '€',
        //         'exchange_rate' => 1,
        //         'is_enabled' => true,
        //         'is_default' => true,
        //     ]);

        Currency::query()
            ->create([
                'country_id' => Country::where('iso_2', 'IT')->value('id'),
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 1,
                'is_enabled' => true,
                'is_default' => true,
            ]);
    }
}
