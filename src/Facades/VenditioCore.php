<?php

namespace PictaStudio\VenditioCore\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use PictaStudio\VenditioCore\Packages\Tools\PackageType;

/**
 * @see PictaStudio\VenditioCore\VenditioCore
 *
 * @method static void configureRateLimiting(string $prefix)
 * @method static void configureUsing(Closure $callback)
 * @method static void registerPolicies()
 * @method static void packageType(PackageType $packageType)
 * @method static PackageType getPackageType()
 * @method static bool isSimple()
 * @method static bool isAdvanced()
 */
class VenditioCore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'venditio-core';

        // return \PictaStudio\VenditioCore\VenditioCore::class;
    }
}
