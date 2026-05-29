<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('product_collection_product', 'sort_order')) {
            return;
        }

        Schema::table('product_collection_product', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('product_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('product_collection_product', 'sort_order')) {
            return;
        }

        Schema::table('product_collection_product', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
