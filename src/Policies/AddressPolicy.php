<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Packages\Simple\Models\Address;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class AddressPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'address';

    public function viewAny(User $user): bool
    {
        return $this->authorize('viewAny', $user);
    }

    public function view(User $user, Address $address): bool
    {
        return $this->belongsToUser($user, $address) &&
            $this->authorize('view', $user, $address);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Address $address): bool
    {
        return $this->belongsToUser($user, $address) &&
            $this->authorize('update', $user, $address);
    }

    public function delete(User $user, Address $address): bool
    {
        return $this->belongsToUser($user, $address) &&
            $this->authorize('delete', $user, $address);
    }

    public function restore(User $user, Address $address): bool
    {
        return $this->belongsToUser($user, $address) &&
            $this->authorize('restore', $user, $address);
    }

    public function forceDelete(User $user, Address $address): bool
    {
        return $this->belongsToUser($user, $address) &&
            $this->authorize('forceDelete', $user, $address);
    }

    public function belongsToUser(User $user, Address $address): bool
    {
        return $user->getMorphClass() === $address->addressable_type &&
            $user->getKey() === $address->addressable_id;
    }
}
