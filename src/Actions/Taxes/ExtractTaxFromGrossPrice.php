<?php

namespace PictaStudio\VenditioCore\Actions\Taxes;

class ExtractTaxFromGrossPrice
{
    /**
     * Returns a tax breakdown from a tax-inclusive price.
     *
     * If rounded taxable + rounded tax does not exactly match the gross price,
     * the tax is rounded up and taxable is adjusted down to keep totals coherent.
     *
     * @return array{taxable: float, tax: float}
     */
    public function handle(float $grossPrice, float $taxRate): array
    {
        $grossCents = max(0, (int) round($grossPrice * 100));

        if ($grossCents === 0 || $taxRate <= 0) {
            return [
                'taxable' => (float) ($grossCents / 100),
                'tax' => 0.0,
            ];
        }

        $divisor = 1 + ($taxRate / 100);
        $rawTaxable = ($grossCents / 100) / $divisor;
        $rawTax = ($grossCents / 100) - $rawTaxable;

        $taxableCents = (int) round($rawTaxable * 100);
        $taxCents = (int) round($rawTax * 100);

        if (($taxableCents + $taxCents) !== $grossCents) {
            $taxCents = (int) ceil(($rawTax * 100) - 1e-9);
            $taxCents = min($taxCents, $grossCents);
            $taxableCents = $grossCents - $taxCents;
        }

        return [
            'taxable' => (float) ($taxableCents / 100),
            'tax' => (float) ($taxCents / 100),
        ];
    }
}
