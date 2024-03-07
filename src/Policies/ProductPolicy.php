<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Models\Product;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class ProductPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'product';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, Product $product): bool
    {
        return $this->authorize('view', $user, $product);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->authorize('update', $user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->authorize('delete', $user, $product);
    }

    public function restore(User $user, Product $product): bool
    {
        return $this->authorize('restore', $user, $product);
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $this->authorize('forceDelete', $user, $product);
    }
}
