<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductItem;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductVariantOption;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_configuration', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductItem::class);
            $table->foreignIdFor(ProductVariantOption::class);
            $table->datetimes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_configuration');
    }
};
