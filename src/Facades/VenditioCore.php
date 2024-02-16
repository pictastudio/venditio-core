<?php

namespace PictaStudio\VenditioCore\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PictaStudio\VenditioCore\VenditioCore
 */
class VenditioCore extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \PictaStudio\VenditioCore\VenditioCore::class;
    }
}
