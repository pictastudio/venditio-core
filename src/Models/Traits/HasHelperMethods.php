<?php

namespace PictaStudio\VenditioCore\Models\Traits;

use Illuminate\Support\Facades\Schema;

trait HasHelperMethods
{
    public static function getService(): string
    {
        return str(self::class)
            ->classBasename()
            ->prepend('App\\Services\\')
            ->append('Service')
            ->toString();
    }

    public static function getTableName(): string
    {
        return app(static::class)->getTable();
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
