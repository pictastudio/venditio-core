<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasDefault
{
    public function scopeDefault(): Builder
    {
        return $this->where('is_default', true);
    }

    public static function getDefault(): static
    {
        return static::where('is_default', true)->first();
    }
}
