<?php

namespace PictaStudio\Venditio\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\Venditio\Models\Brand;
use PictaStudio\Venditio\Policies\Traits\VenditioPolicyPermissions;

class BrandPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'brand';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, Brand $brand): bool
    {
        return $this->authorize('view', $user, $brand);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Brand $brand): bool
    {
        return $this->authorize('update', $user, $brand);
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $this->authorize('delete', $user, $brand);
    }

    public function restore(User $user, Brand $brand): bool
    {
        return $this->authorize('restore', $user, $brand);
    }

    public function forceDelete(User $user, Brand $brand): bool
    {
        return $this->authorize('forceDelete', $user, $brand);
    }
}
