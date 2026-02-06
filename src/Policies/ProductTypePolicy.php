<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Packages\Advanced\Models\ProductType;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class ProductTypePolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'product-type';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, ProductType $productType): bool
    {
        return $this->authorize('view', $user, $productType);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, ProductType $productType): bool
    {
        return $this->authorize('update', $user, $productType);
    }

    public function delete(User $user, ProductType $productType): bool
    {
        return $this->authorize('delete', $user, $productType);
    }

    public function restore(User $user, ProductType $productType): bool
    {
        return $this->authorize('restore', $user, $productType);
    }

    public function forceDelete(User $user, ProductType $productType): bool
    {
        return $this->authorize('forceDelete', $user, $productType);
    }
}
