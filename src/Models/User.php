<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\VenditioCore\Models\Traits\HasAddresses;

class User extends Authenticatable
{
    use HasAddresses;

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
