<?php

namespace PictaStudio\Venditio\Models\Scopes;

use Illuminate\Database\Eloquent\{Builder, Model, Scope};

class Active implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (request()->routeIs(config('venditio.scopes.routes_to_exclude'))) {
            return;
        }

        $builder->where('active', true);
    }
}
