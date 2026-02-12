<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('discountable');
            $table->string('type', 20)->comment('percentage, fixed');
            $table->decimal('value', 10, 2);
            $table->string('name')->nullable();
            $table->string('code', 50)->unique();
            $table->boolean('active')->default(true);
            $table->dateTime('starts_at')->comment('the datetime the discount starts at');
            $table->dateTime('ends_at')->nullable()->comment('the datetime the discount ends at, if NULL it won\'t expire');
            $table->unsignedInteger('uses')->default(0)->comment('how many times the discount has been used');
            $table->unsignedInteger('max_uses')->nullable()->comment('how many times the discount can be used');
            $table->boolean('apply_to_cart_total')->default(false)->comment('whether this discount is applied to cart total instead of each line');
            $table->boolean('apply_once_per_cart')->default(false)->comment('whether this discount can be applied only once per cart');
            $table->unsignedInteger('max_uses_per_user')->nullable()->comment('how many times a single user can use this discount');
            $table->boolean('one_per_user')->default(false)->comment('if true, this discount can be used only once per user');
            $table->boolean('free_shipping')->default(false)->comment('if true, shipping fee is set to zero when discount is applied');
            $table->decimal('minimum_order_total', 10, 2)->nullable()->comment('minimum cart/order total required to apply discount');
            $table->integer('priority')->default(0)->comment('the order of priority');
            $table->boolean('stop_after_propagation')->default(false)->comment('whether this discount will stop others after propagating');
            $table->datetimes();
            $table->softDeletesDatetime();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
