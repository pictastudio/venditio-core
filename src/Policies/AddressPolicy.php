<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use PictaStudio\VenditioCore\Models\Address;
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
        return $this->authorize('view', $user, $address);
    }

    public function create(User $user): bool
    {
        return $this->authorize('create', $user);
    }

    public function update(User $user, Address $address): bool
    {
        return $this->authorize('update', $user, $address);
    }

    public function delete(User $user, Address $address): bool
    {
        return $this->authorize('delete', $user, $address);
    }

    public function restore(User $user, Address $address): bool
    {
        return $this->authorize('restore', $user, $address);
    }

    public function forceDelete(User $user, Address $address): bool
    {
        return $this->authorize('forceDelete', $user, $address);
    }
}
