<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Models\Order;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class OrderPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'order';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, Order $order): bool
    {
        return $this->belongsToUser($user, $order) &&
            $this->authorize('view', $user, $order);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Order $order): bool
    {
        return $this->belongsToUser($user, $order) &&
            $this->authorize('update', $user, $order);
    }

    public function delete(User $user, Order $order): bool
    {
        return $this->belongsToUser($user, $order) &&
            $this->authorize('delete', $user, $order);
    }

    public function restore(User $user, Order $order): bool
    {
        return $this->belongsToUser($user, $order) &&
            $this->authorize('restore', $user, $order);
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $this->belongsToUser($user, $order) &&
            $this->authorize('forceDelete', $user, $order);
    }

    public function belongsToUser(User $user, Order $order): bool
    {
        return $order->user_id === $user->getKey();
    }
}
