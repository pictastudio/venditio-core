<?php

namespace PictaStudio\VenditioCore\Managers\Contracts;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PictaStudio\VenditioCore\Models\Contracts\User;

interface AuthManager
{
    public function user(User|Authenticatable $user): static;

    public function getUser(): User|Authenticatable|null;

    public function can(string $resource, string $action): bool;

    public static function generatePermissionName(string $resource, string $action): string;

    public static function getPermissions(?string $resource = null): array;
}
