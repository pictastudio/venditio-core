<?php

namespace PictaStudio\VenditioCore\Formatters\Pricing;

use NumberFormatter;
use PictaStudio\VenditioCore\Formatters\Pricing\Contracts\PriceFormatter;
use PictaStudio\VenditioCore\Models\Currency;

class DefaultPriceFormatter implements PriceFormatter
{
    public function __construct(
        public int $value,
        public ?Currency $currency = null,
        public int $unitQty = 1
    ) {
        $this->currency ??= Currency::getDefault();
    }

    public function decimal(bool $rounding = true): float
    {
        $convertedValue = $this->value / 100;

        return $rounding
            ? round($convertedValue, $this->currency->decimal_places)
            : $convertedValue;
    }

    public function unitDecimal(bool $rounding = true): float
    {
        $convertedValue = $this->value / 100 / $this->unitQty;

        return $rounding
            ? round($convertedValue, $this->currency->decimal_places)
            : $convertedValue;
    }

    public function formatted(?string $locale = null, string $formatterStyle = NumberFormatter::CURRENCY, ?int $decimalPlaces = null, bool $trimTrailingZeros = true): mixed
    {
        return $this->formatValue($this->decimal(false), $locale, $formatterStyle, $decimalPlaces, $trimTrailingZeros);
    }

    public function unitFormatted(?string $locale = null, string $formatterStyle = NumberFormatter::CURRENCY, ?int $decimalPlaces = null, bool $trimTrailingZeros = true): mixed
    {
        return $this->formatValue($this->unitDecimal(false), $locale, $formatterStyle, $decimalPlaces, $trimTrailingZeros);
    }

    protected function formatValue(int|float $value, ?string $locale = null, string $formatterStyle = NumberFormatter::CURRENCY, ?int $decimalPlaces = null, bool $trimTrailingZeros = true): mixed
    {
        if (!$locale) {
            $locale = app()->currentLocale();
        }

        $formatter = new NumberFormatter($locale, $formatterStyle);

        $formatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, $this->currency->code);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimalPlaces ?? $this->currency->decimal_places);
        $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, app()->getLocale() === 'it' ? ',' : '.');
        $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, app()->getLocale() === 'it' ? '.' : ',');

        $formattedPrice = $formatter->format($value);

        if ($trimTrailingZeros) {
            $decimalSeparator = $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

            $formattedPrice = preg_replace('/(\\' . $decimalSeparator . '\d{' . $this->currency->decimal_places . '}\d*?)0+(\s*\D*)$/', '$1$2', $formattedPrice);
        }

        return $formattedPrice;
    }
}
