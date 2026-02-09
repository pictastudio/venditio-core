<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Models\ProductVariant;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class ProductVariantPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'product-variant';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, ProductVariant $productVariant): bool
    {
        return $this->authorize('view', $user, $productVariant);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, ProductVariant $productVariant): bool
    {
        return $this->authorize('update', $user, $productVariant);
    }

    public function delete(User $user, ProductVariant $productVariant): bool
    {
        return $this->authorize('delete', $user, $productVariant);
    }

    public function restore(User $user, ProductVariant $productVariant): bool
    {
        return $this->authorize('restore', $user, $productVariant);
    }

    public function forceDelete(User $user, ProductVariant $productVariant): bool
    {
        return $this->authorize('forceDelete', $user, $productVariant);
    }
}
