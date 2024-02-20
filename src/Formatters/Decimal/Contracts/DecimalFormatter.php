<?php

namespace PictaStudio\VenditioCore\Formatters\Decimal\Contracts;

interface DecimalFormatter
{
    public function decimal(): float;

    public function unitDecimal(): float;

    public function formatted(): mixed;

    public function unitFormatted(): mixed;
}
