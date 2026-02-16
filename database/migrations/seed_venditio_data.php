<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\Venditio\Models\{Country, Currency, ProductType, TaxClass};
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->seedCountries();
        $this->seedCurrencies();
        $this->seedTaxClasses();
        $this->seedProductTypes();
    }

    private function seedCountries(): void
    {
        $countries = File::json(__DIR__ . '/../seeders/data/countries.json');

        $countries = collect($countries)
            ->map(function (array $country) {
                return array_merge($country, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })
            ->toArray();

        Country::unguard();

        Country::insert($countries);

        Country::reguard();
    }

    private function seedCurrencies(): void
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

    private function seedTaxClasses(): void
    {
        $taxClass = TaxClass::query()->create([
            'name' => 'Standard',
            'is_default' => true,
        ]);

        $taxClass->countries()->attach(Country::where('iso_2', 'IT')->value('id'), [
            'rate' => 22,
        ]);
    }

    private function seedProductTypes(): void
    {
        ProductType::query()->create([
            'name' => 'Default',
            'active' => true,
            'is_default' => true,
        ]);
    }
};
