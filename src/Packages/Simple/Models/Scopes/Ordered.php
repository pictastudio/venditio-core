<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models\Scopes;

use Illuminate\Database\Eloquent\{Builder, Model, Scope};

class Ordered implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->orderBy('sort_order', 'asc');
    }
}
