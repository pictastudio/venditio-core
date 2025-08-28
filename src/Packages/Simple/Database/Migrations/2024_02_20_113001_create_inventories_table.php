<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema};
use PictaStudio\VenditioCore\Packages\Simple\Models\Product;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class);
            $table->mediumInteger('stock')->default(0);
            $table->mediumInteger('stock_reserved')->default(0)->comment('quantity of stock that has been reserved (for pending orders or for other reasons)');
            $table->mediumInteger('stock_available')->default(0)->comment('quantity of stock available for sale');
            $table->mediumInteger('stock_min')->nullable()->comment('minimum stock quantity (for low stock alert)');
            $table->decimal('price', 10, 2);
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
