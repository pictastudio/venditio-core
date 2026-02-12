<?php

namespace PictaStudio\Venditio\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\Venditio\Models\Traits\{HasAddresses, HasDiscounts, HasHelperMethods, LogsActivity};
use Spatie\Permission\Traits\HasRoles;

use function PictaStudio\Venditio\Helpers\Functions\resolve_model;

class User extends Authenticatable
{
    use HasAddresses;
    use HasDiscounts;
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
        return $this->hasMany(resolve_model('cart'));
    }

    public function orders(): HasMany
    {
        return $this->hasMany(resolve_model('order'));
    }

    public function isRoot(): bool
    {
        return $this->hasRole(config('venditio.auth.roles.root'));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(config('venditio.auth.roles.admin'));
    }

    public function isUser(): bool
    {
        return $this->hasRole(config('venditio.auth.roles.user'));
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => (
                $this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name')
            ),
        );
    }
}
