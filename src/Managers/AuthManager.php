<?php

namespace PictaStudio\VenditioCore\Managers;

use PictaStudio\VenditioCore\Models\User;

class AuthManager
{
    const ROLE_ROOT = 'root';

    const ROLE_ADMIN = 'admin';

    const ROLE_USER = 'user';

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

    public static function getPermissionName(string $resource, string $action): string
    {
        return $resource . ':' . $action;
    }

    public static function getResources(): array
    {
        return [
            'user',
            'role',
            'address',
            'cart',
            'order',
            'product',
            'productCategory',
            'brand',
        ];
    }

    public static function getActions(): array
    {
        return [
            'create',
            'view',
            'update',
            'delete',
        ];
    }

    public function can(string $resource, string $action): bool
    {
        return $this->user->can($this->getPermissionName($resource, $action));
    }

    public static function getAllRoles(): array
    {
        return [
            self::ROLE_ROOT,
            self::ROLE_ADMIN,
            self::ROLE_USER,
        ];
    }
}
