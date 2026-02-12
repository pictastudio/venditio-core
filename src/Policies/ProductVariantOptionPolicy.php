<?php

namespace PictaStudio\Venditio\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\Venditio\Models\ProductVariantOption;
use PictaStudio\Venditio\Policies\Traits\VenditioPolicyPermissions;

class ProductVariantOptionPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'product-variant-option';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, ProductVariantOption $productVariantOption): bool
    {
        return $this->authorize('view', $user, $productVariantOption);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, ProductVariantOption $productVariantOption): bool
    {
        return $this->authorize('update', $user, $productVariantOption);
    }

    public function delete(User $user, ProductVariantOption $productVariantOption): bool
    {
        return $this->authorize('delete', $user, $productVariantOption);
    }

    public function restore(User $user, ProductVariantOption $productVariantOption): bool
    {
        return $this->authorize('restore', $user, $productVariantOption);
    }

    public function forceDelete(User $user, ProductVariantOption $productVariantOption): bool
    {
        return $this->authorize('forceDelete', $user, $productVariantOption);
    }
}
