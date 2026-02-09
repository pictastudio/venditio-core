<?php

namespace PictaStudio\VenditioCore\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\VenditioCore\Models\User;
use PictaStudio\VenditioCore\Policies\Traits\VenditioPolicyPermissions;

class UserPolicy
{
    use HandlesAuthorization;
    use VenditioPolicyPermissions;

    public string $resource = 'user';

    public function viewAny(Authenticatable $authenticatable): bool
    {
        return $this->authorize('viewAny', $authenticatable);
    }

    public function view(Authenticatable $authenticatable, User $user): bool
    {
        return $this->authorize('view', $authenticatable, $user);
    }

    public function create(Authenticatable $authenticatable): bool
    {
        return $this->authorize('create', $authenticatable);
    }

    public function update(Authenticatable $authenticatable, User $user): bool
    {
        return $this->authorize('update', $authenticatable, $user);
    }

    public function delete(Authenticatable $authenticatable, User $user): bool
    {
        return $this->authorize('delete', $authenticatable, $user);
    }

    public function restore(Authenticatable $authenticatable, User $user): bool
    {
        return $this->authorize('restore', $authenticatable, $user);
    }

    public function forceDelete(Authenticatable $authenticatable, User $user): bool
    {
        return $this->authorize('forceDelete', $authenticatable, $user);
    }
}
