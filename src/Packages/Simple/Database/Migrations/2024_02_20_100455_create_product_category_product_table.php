<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;
use PictaStudio\VenditioCore\Packages\Simple\Models\ProductCategory;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductCategory::class);
            $table->foreignIdFor(Product::class);
            $table->datetimes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_category_product');
    }
};
