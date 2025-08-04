<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models\Traits;

use Illuminate\Support\Facades\Schema;

trait HasHelperMethods
{
    public static function getTableName(): string
    {
        return app(static::class)->getTable();
    }

    public static function getKeyName(): string
    {
        return app(static::class)->getKeyName();
    }

    public static function getResourceLabel(): string
    {
        return str(static::class)
            ->classBasename()
            ->snake()
            ->lower()
            ->toString();
    }

    public static function getTableColumns(): array
    {
        return Schema::getColumnListing(static::getTableName());
    }

    public function getTableFillableColumns(): array
    {
        $columns = static::getTableColumns();
        $columnsToExclude = $this->getGuarded();

        return array_diff($columns, $columnsToExclude);
    }

    public function getColumnType(string $column): string
    {
        return Schema::getColumnType(static::getTableName(), $column);
    }
}
