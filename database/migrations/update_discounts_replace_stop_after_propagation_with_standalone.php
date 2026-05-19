<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'standalone')) {
                $table->boolean('standalone')
                    ->default(false)
                    ->after('priority')
                    ->comment('whether this discount must be applied alone');
            }

            if (Schema::hasColumn('discounts', 'stop_after_propagation')) {
                $table->dropColumn('stop_after_propagation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('discounts', 'stop_after_propagation')) {
                $table->boolean('stop_after_propagation')
                    ->default(false)
                    ->after('priority')
                    ->comment('whether this discount will stop others after propagating');
            }

            if (Schema::hasColumn('discounts', 'standalone')) {
                $table->dropColumn('standalone');
            }
        });
    }
};
