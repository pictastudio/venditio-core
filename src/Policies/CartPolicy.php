<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Packages\Simple\Models\Cart;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class CartPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'cart';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, Cart $cart): bool
    {
        return $this->belongsToUser($user, $cart) &&
            $this->authorize('view', $user, $cart);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Cart $cart): bool
    {
        return $this->belongsToUser($user, $cart) &&
            $this->authorize('update', $user, $cart);
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $this->belongsToUser($user, $cart) &&
            $this->authorize('delete', $user, $cart);
    }

    public function restore(User $user, Cart $cart): bool
    {
        return $this->belongsToUser($user, $cart) &&
            $this->authorize('restore', $user, $cart);
    }

    public function forceDelete(User $user, Cart $cart): bool
    {
        return $this->belongsToUser($user, $cart) &&
            $this->authorize('forceDelete', $user, $cart);
    }

    public function belongsToUser(User $user, Cart $cart): bool
    {
        return $cart->user_id === $user->getKey();
    }
}
