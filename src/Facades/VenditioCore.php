<?php

namespace PictaStudio\VenditioCore\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PictaStudio\VenditioCore\VenditioCore
 *
 * @method static void configureRateLimiting(string $prefix)
 */
class VenditioCore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'venditio-core';

        return \PictaStudio\VenditioCore\VenditioCore::class;
    }
}
