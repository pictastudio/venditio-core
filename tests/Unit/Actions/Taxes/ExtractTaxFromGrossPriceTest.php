<?php

use PictaStudio\VenditioCore\Actions\Taxes\ExtractTaxFromGrossPrice;

it('extracts taxable amount and tax from gross price', function () {
    $result = app(ExtractTaxFromGrossPrice::class)->handle(122.00, 22.00);

    expect($result['taxable'])->toBe(100.00)
        ->and($result['tax'])->toBe(22.00);
});

it('returns zero tax when tax rate is not positive', function () {
    $result = app(ExtractTaxFromGrossPrice::class)->handle(99.99, 0.00);

    expect($result['taxable'])->toBe(99.99)
        ->and($result['tax'])->toBe(0.00);
});

it('rounds tax up and adjusts taxable down when rounding is inconsistent', function (
    float $gross,
    float $rate,
    float $expectedTax,
    float $expectedTaxable
) {
    $result = app(ExtractTaxFromGrossPrice::class)->handle($gross, $rate);

    expect($result['tax'])->toBe($expectedTax)
        ->and($result['taxable'])->toBe($expectedTaxable)
        ->and(round($result['taxable'] + $result['tax'], 2))->toBe($gross);
})->with([
    'low price with decimal VAT' => [9.39, 0.16, 0.02, 9.37],
    'higher price with 22% VAT'  => [87.23, 22.00, 15.73, 71.50],
]);
