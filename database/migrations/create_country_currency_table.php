<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\Venditio\Models\{Country, Currency};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_currency', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class);
            $table->foreignIdFor(Currency::class);
            $table->datetimes();

            $table->unique(['country_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_currency');
    }
};
