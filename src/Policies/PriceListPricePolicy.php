<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Models\PriceListPrice;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class PriceListPricePolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'price-list-price';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, PriceListPrice $priceListPrice): bool
    {
        return $this->authorize('view', $user, $priceListPrice);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, PriceListPrice $priceListPrice): bool
    {
        return $this->authorize('update', $user, $priceListPrice);
    }

    public function delete(User $user, PriceListPrice $priceListPrice): bool
    {
        return $this->authorize('delete', $user, $priceListPrice);
    }

    public function restore(User $user, PriceListPrice $priceListPrice): bool
    {
        return $this->authorize('restore', $user, $priceListPrice);
    }

    public function forceDelete(User $user, PriceListPrice $priceListPrice): bool
    {
        return $this->authorize('forceDelete', $user, $priceListPrice);
    }
}
