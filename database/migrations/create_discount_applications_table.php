<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id')->index();
            $table->nullableMorphs('discountable');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('cart_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedBigInteger('order_line_id')->nullable()->unique();
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_applications');
    }
};
