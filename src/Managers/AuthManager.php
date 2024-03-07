<?php

namespace PictaStudio\VenditioCore\Managers;

use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as ContractsAuthManager;
use PictaStudio\VenditioCore\Models\User;

class AuthManager implements ContractsAuthManager
{
    const ROLE_ROOT = 'Root';

    const ROLE_ADMIN = 'Admin';

    const ROLE_USER = 'User';

    public function __construct(public User $user)
    {
    }

    public static function make(User $user): static
    {
        return new static($user);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function can(string $resource, string $action): bool
    {
        return $this->user->can($this->generatePermissionName($resource, $action));
    }

    public static function generatePermissionName(string $resource, string $action): string
    {
        return $resource . ':' . $action;
    }

    public static function getPermissions(?string $resource = null): array
    {
        $resources = config('venditio-core.auth.resources');
        $actions = config('venditio-core.auth.actions');

        if ($resource) {
            return static::generatePermission($resource, $actions);
        }

        return collect($resources)
            ->map(fn (string $resource) => static::generatePermission($resource, $actions))
            ->merge(static::getExtraPermissions())
            ->flatten()
            ->toArray();
    }

    private static function getExtraPermissions(): array
    {
        return collect(config('venditio-core.auth.extra_permissions', []))
            ->map(fn (array $actions, string $resource) => static::generatePermission($resource, $actions))
            ->flatten()
            ->toArray();
    }

    private static function generatePermission(string $resource, array $actions): array
    {
        return collect($actions)
            ->map(fn (string $action) => static::generatePermissionName($resource, $action))
            ->toArray();
    }
}
