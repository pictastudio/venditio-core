<?php

namespace PictaStudio\Venditio\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\Venditio\Discounts\DiscountContext;

interface CartTotalDiscountCalculatorInterface
{
    /**
     * @param  Collection<int, Model>  $lines
     * @return array{discount_id: int|null, discount_code: string|null, discount_amount: float, free_shipping: bool}
     */
    public function resolveForTarget(Model $target, Collection $lines, DiscountContext $context): array;
}
