<?php

namespace PictaStudio\Venditio\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @see PictaStudio\Venditio\Venditio
 *
 * @method static void configureRateLimiting(string $prefix)
 * @method static void configureUsing(Closure $callback)
 */
class Venditio extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'venditio';
    }
}
