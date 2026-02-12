<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\Venditio\Models\{Product, ProductVariantOption};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_configuration', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(ProductVariantOption::class);
            $table->datetimes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_configuration');
    }
};
