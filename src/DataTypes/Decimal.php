<?php

namespace PictaStudio\VenditioCore\DataTypes;

use PictaStudio\VenditioCore\Exceptions\InvalidDataTypeValueException;
use PictaStudio\VenditioCore\Formatters\Decimal\DefaultDecimalFormatter;

class Decimal
{
    public function __construct(
        public mixed $value,
        public int $unitQty = 1
    ) {
        if (!is_int($value)) {
            throw new InvalidDataTypeValueException(
                'Value was "' . (gettype($value)) . '" expected "int"'
            );
        }
    }

    /**
     * Getter for methods/properties.
     */
    public function __get(string $name)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}();
        }
    }

    /**
     * Cast class as a string.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    private function formatter()
    {
        return app(
            config('venditio-core.decimal.formatter', DefaultDecimalFormatter::class),
            [
                'value' => $this->value,
                'unitQty' => $this->unitQty,
            ]
        );
    }

    /**
     * Get the decimal value
     */
    public function decimal(...$arguments): float
    {
        return $this->formatter()->decimal(...$arguments);
    }

    /**
     * Get the decimal unit value
     */
    public function unitDecimal(...$arguments): float
    {
        return $this->formatter()->unitDecimal(...$arguments);
    }

    /**
     * Format the value
     *
     * @return string
     */
    public function formatted(...$arguments): mixed
    {
        return $this->formatter()->formatted(...$arguments);
    }

    /**
     * Format the unit value
     *
     * @return string
     */
    public function unitFormatted(...$arguments): mixed
    {
        return $this->formatter()->unitFormatted(...$arguments);
    }

    protected function formatValue(int|float $value, ...$arguments): mixed
    {
        return $this->formatter()->formatValue($value, ...$arguments);
    }
}
