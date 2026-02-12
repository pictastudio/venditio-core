<?php

namespace PictaStudio\Venditio\Managers\Contracts;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\Venditio\Models\User;

interface AuthManager
{
    public static function generatePermissionName(string $resource, string $action): string;

    public static function getPermissions(?string $resource = null): array;

    public function user(User|Authenticatable $user): static;

    public function getUser(): User|Authenticatable|null;

    public function can(string $resource, string $action): bool;
}
