<?php

namespace PictaStudio\VenditioCore\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use PictaStudio\VenditioCore\Models\Address;

trait HasAddresses
{
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
