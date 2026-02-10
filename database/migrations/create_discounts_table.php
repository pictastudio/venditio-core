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
            $table->morphs('discountable');
            $table->string('type', 20)->comment('percentage, fixed');
            $table->decimal('value', 10, 2);
            $table->string('name')->nullable();
            $table->string('code', 50)->unique();
            $table->boolean('active')->default(true);
            $table->dateTime('starts_at')->comment('the datetime the discount starts at');
            $table->dateTime('ends_at')->nullable()->comment('the datetime the discount ends at, if NULL it won\'t expire');
            $table->unsignedInteger('uses')->default(0)->comment('how many times the discount has been used');
            $table->unsignedInteger('max_uses')->nullable()->comment('how many times the discount can be used');
            $table->json('rules')->nullable()->comment('rule options, e.g. {"max_uses_per_user": 1, "apply_once_per_cart": true}');
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
