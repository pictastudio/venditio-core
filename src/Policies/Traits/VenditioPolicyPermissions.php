<?php

namespace PictaStudio\VenditioCore\Policies\Traits;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;

use function PictaStudio\VenditioCore\Helpers\Functions\auth_manager;

trait VenditioPolicyPermissions
{
    public function authorize(string $action, User $user): bool
    {
        return auth_manager()->user($user)->can($this->resource, Str::snake($action, '-'));
    }
}
