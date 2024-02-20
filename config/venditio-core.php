<?php

use PictaStudio\VenditioCore\Formatters\Decimal\DefaultDecimalFormatter;
use PictaStudio\VenditioCore\Formatters\Pricing\DefaultPriceFormatter;

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

    /*
    |--------------------------------------------------------------------------
    | Decimal
    |--------------------------------------------------------------------------
    |
    | Specify the decimal formatter
    |
    */
    'decimal' => [
        'formatter' => DefaultDecimalFormatter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Scopes configuration
    |
    */
    'scopes' => [
        'in_date_range' => [
            'allow_null' => true, // allow null values to pass when checking date range
            'include_start_date' => true, // include the start date in the date range
            'include_end_date' => true, // include the end date in the date range
        ],
    ],
];
