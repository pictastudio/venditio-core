<?php

namespace PictaStudio\VenditioCore\Models\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasDefault
{
    #[Scope]
    public function default(Builder $builder): Builder
    {
        return $builder->where('is_default', true);
    }

    public static function getDefault(): static
    {
        return static::where('is_default', true)->first();
    }
}
