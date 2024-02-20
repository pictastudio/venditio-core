<?php

namespace PictaStudio\VenditioCore\Formatters\Decimal;

use NumberFormatter;
use PictaStudio\VenditioCore\Formatters\Decimal\Contracts\DecimalFormatter;

class DefaultDecimalFormatter implements DecimalFormatter
{
    public function __construct(
        public int $value,
        public int $unitQty = 1,
        public int $decimalPlaces = 2,
    ) {
    }

    public function decimal(bool $rounding = true): float
    {
        $convertedValue = $this->value / 10;

        return $rounding
            ? round($convertedValue, $this->decimalPlaces)
            : $convertedValue;
    }

    public function unitDecimal(bool $rounding = true): float
    {
        $convertedValue = $this->value / 10 / $this->unitQty;

        return $rounding
            ? round($convertedValue, $this->decimalPlaces)
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

        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimalPlaces ?? $this->decimalPlaces);
        $formatter->setAttribute(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, app()->getLocale() === 'it' ? ',' : '.');
        $formatter->setAttribute(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, app()->getLocale() === 'it' ? '.' : ',');

        $formattedPrice = $formatter->format($value);

        if ($trimTrailingZeros) {
            $decimalSeparator = $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

            $formattedPrice = preg_replace('/(\\' . $decimalSeparator . '\d{' . $this->decimalPlaces . '}\d*?)0+(\s*\D*)$/', '$1$2', $formattedPrice);
        }

        return $formattedPrice;
    }
}
