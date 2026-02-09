<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Models\ProductCategory;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class ProductCategoryPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'product-category';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, ProductCategory $productCategory): bool
    {
        return $this->authorize('view', $user, $productCategory);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, ProductCategory $productCategory): bool
    {
        return $this->authorize('update', $user, $productCategory);
    }

    public function delete(User $user, ProductCategory $productCategory): bool
    {
        return $this->authorize('delete', $user, $productCategory);
    }

    public function restore(User $user, ProductCategory $productCategory): bool
    {
        return $this->authorize('restore', $user, $productCategory);
    }

    public function forceDelete(User $user, ProductCategory $productCategory): bool
    {
        return $this->authorize('forceDelete', $user, $productCategory);
    }
}
