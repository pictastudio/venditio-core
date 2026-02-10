<?php

namespace PictaStudio\VenditioCore\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

trait HasDiscounts
{
    public function discounts(): MorphMany
    {
        return $this->morphMany(resolve_model('discount'), 'discountable');
    }
}
