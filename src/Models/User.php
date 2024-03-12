<?php

namespace PictaStudio\VenditioCore\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\VenditioCore\Models\Traits\HasAddresses;
use PictaStudio\VenditioCore\Models\Traits\HasHelperMethods;
use PictaStudio\VenditioCore\Models\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasAddresses;
    use HasHelperMethods;
    use HasRoles;
    use LogsActivity;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function carts(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.cart'));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(config('venditio-core.models.order'));
    }

    public function isRoot(): bool
    {
        return $this->hasRole(config('venditio-core.auth.roles.root'));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(config('venditio-core.auth.roles.admin'));
    }

    public function isUser(): bool
    {
        return $this->hasRole(config('venditio-core.auth.roles.user'));
    }
}
