<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = [
        'products',
        'brands',
        'product_categories',
        'tags',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $this->renameColumnIfPresent($table, 'in_evidence', 'highlighted');
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            $this->renameColumnIfPresent($table, 'highlighted', 'in_evidence');
        }
    }

    private function renameColumnIfPresent(string $tableName, string $from, string $to): void
    {
        if (
            !Schema::hasTable($tableName)
            || !Schema::hasColumn($tableName, $from)
            || Schema::hasColumn($tableName, $to)
        ) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($from, $to): void {
            $table->renameColumn($from, $to);
        });
    }
};
