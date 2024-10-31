<?php

namespace PictaStudio\VenditioCore\Packages\Simple\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use PictaStudio\VenditioCore\Packages\Simple\Models\Address;

trait HasAddresses
{
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
