<?php

use PictaStudio\VenditioCore\Pricing\DefaultPriceFormatter;

return [

    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    |
    | Specify the pricing formatter
    |
    */
    'pricing' => [
        'formatter' => DefaultPriceFormatter::class,
    ],
];
