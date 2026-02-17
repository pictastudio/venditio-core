<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\Venditio\Models\Province;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Province::class);
            $table->string('name');
            $table->string('country_zone', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('phone_prefix', 20)->nullable();
            $table->string('istat_code', 20)->nullable()->index();
            $table->string('cadastral_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipalities');
    }
};
