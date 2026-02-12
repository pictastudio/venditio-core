<?php

namespace PictaStudio\VenditioCore\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
/**
 * @see PictaStudio\VenditioCore\VenditioCore
 *
 * @method static void configureRateLimiting(string $prefix)
 * @method static void configureUsing(Closure $callback)
 * @method static void registerPolicies()
 */
class VenditioCore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'venditio-core';
    }
}
