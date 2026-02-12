<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\Venditio\Models\ProductType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductType::class);
            $table->string('name');
            $table->boolean('required')->default(false);
            $table->smallInteger('sort_order');
            $table->string('type');
            $table->json('options')->nullable();
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_custom_fields');
    }
};
