<?php

namespace PictaStudio\VenditioCore\Models\Scopes;

use Illuminate\Database\Eloquent\{Builder, Model, Scope};

class Active implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (request()->routeIs(config('venditio-core.scopes.routes_to_exclude'))) {
            return;
        }

        $builder->where('active', true);
    }
}
