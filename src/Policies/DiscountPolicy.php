<?php

namespace PictaStudio\Venditio\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\Venditio\Models\Discount;
use PictaStudio\Venditio\Policies\Traits\VenditioPolicyPermissions;

class DiscountPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'discount';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, Discount $discount): bool
    {
        return $this->authorize('view', $user, $discount);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Discount $discount): bool
    {
        return $this->authorize('update', $user, $discount);
    }

    public function delete(User $user, Discount $discount): bool
    {
        return $this->authorize('delete', $user, $discount);
    }

    public function restore(User $user, Discount $discount): bool
    {
        return $this->authorize('restore', $user, $discount);
    }

    public function forceDelete(User $user, Discount $discount): bool
    {
        return $this->authorize('forceDelete', $user, $discount);
    }
}
