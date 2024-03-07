<?php

namespace PictaStudio\VenditioCore\Managers\Contracts;

use PictaStudio\VenditioCore\Models\User;

interface AuthManager
{
    public function getUser(): User;

    public function can(string $resource, string $action): bool;

    public static function generatePermissionName(string $resource, string $action): string;

    public static function getPermissions(?string $resource = null): array;
}
