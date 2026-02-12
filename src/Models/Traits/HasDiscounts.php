<?php

namespace PictaStudio\Venditio\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

trait HasDiscounts
{
    public function discounts(): MorphMany
    {
        return $this->morphMany(resolve_model('discount'), 'discountable');
    }
}
