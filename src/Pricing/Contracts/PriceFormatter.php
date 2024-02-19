<?php

namespace PictaStudio\VenditioCore\Pricing\Contracts;

interface PriceFormatter
{
    public function decimal(): float;

    public function unitDecimal(): float;

    public function formatted(): mixed;

    public function unitFormatted(): mixed;
}
