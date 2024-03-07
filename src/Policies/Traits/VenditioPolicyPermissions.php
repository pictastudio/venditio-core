<?php

namespace PictaStudio\VenditioCore\Policies\Traits;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;

trait VenditioPolicyPermissions
{
    public function authorize(string $action, User $user): bool
    {
        return config('venditio-core.auth.manager')::make($user)->can($this->resource, Str::snake($action, '-'));
    }
}
