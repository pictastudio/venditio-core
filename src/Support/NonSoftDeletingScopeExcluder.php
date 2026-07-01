<?php

namespace PictaStudio\Venditio\Support;

use Illuminate\Database\Eloquent\{Builder, SoftDeletingScope};

class NonSoftDeletingScopeExcluder
{
    public static function apply(Builder $query): Builder
    {
        return $query->withoutGlobalScopesExcept([
            SoftDeletingScope::class,
        ]);
    }
}
