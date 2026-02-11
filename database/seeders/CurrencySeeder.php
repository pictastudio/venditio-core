<?php

namespace PictaStudio\VenditioCore\Database\Seeders;

use Illuminate\Database\Seeder;
use PictaStudio\VenditioCore\Models\Country;
use PictaStudio\VenditioCore\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currency = Currency::query()
            ->create([
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 1,
                'is_enabled' => true,
                'is_default' => true,
            ]);

        $countryId = Country::where('iso_2', 'IT')->value('id');

        if ($countryId) {
            $currency->countries()->attach($countryId);
        }
    }
}
