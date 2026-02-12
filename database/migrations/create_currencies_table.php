<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->char('code', 3)->unique()->comment('ISO 4217 currency code');
            $table->string('symbol', 10)->nullable();
            $table->decimal('exchange_rate', 10, 4);
            $table->tinyInteger('decimal_places')->default(2);
            $table->boolean('is_enabled')->default(false)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
