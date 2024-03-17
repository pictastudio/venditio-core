<?php

namespace PictaStudio\VenditioCore\Policies\Traits;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use PictaStudio\VenditioCore\Managers\Contracts\AuthManager as AuthManagerContract;

trait VenditioPolicyPermissions
{
    public function authorize(string $action, User $user): bool
    {
        return app(AuthManagerContract::class)->user($user)->can($this->resource, Str::snake($action, '-'));
    }
}
