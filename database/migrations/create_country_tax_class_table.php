<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Models\{Country, TaxClass};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_tax_class', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class);
            $table->foreignIdFor(TaxClass::class);
            $table->decimal('rate', 8, 2);
            $table->datetimes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_tax_class');
    }
};
