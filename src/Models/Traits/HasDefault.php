<?php

namespace PictaStudio\VenditioCore\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasDefault
{
    public function scopeDefault(): Builder
    {
        return $this->where('default', true);
    }

    public static function getDefault(): static
    {
        return static::where('default', true)->first();
    }
}
