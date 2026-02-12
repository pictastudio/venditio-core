<?php

namespace PictaStudio\Venditio\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\Venditio\Models\PriceList;
use PictaStudio\Venditio\Policies\Traits\VenditioPolicyPermissions;

class PriceListPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'price-list';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, PriceList $priceList): bool
    {
        return $this->authorize('view', $user, $priceList);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, PriceList $priceList): bool
    {
        return $this->authorize('update', $user, $priceList);
    }

    public function delete(User $user, PriceList $priceList): bool
    {
        return $this->authorize('delete', $user, $priceList);
    }

    public function restore(User $user, PriceList $priceList): bool
    {
        return $this->authorize('restore', $user, $priceList);
    }

    public function forceDelete(User $user, PriceList $priceList): bool
    {
        return $this->authorize('forceDelete', $user, $priceList);
    }
}
