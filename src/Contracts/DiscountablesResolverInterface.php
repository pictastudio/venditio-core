<?php

namespace PictaStudio\VenditioCore\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PictaStudio\VenditioCore\Discounts\DiscountContext;

interface DiscountablesResolverInterface
{
    /**
     * @return Collection<int, Model>
     */
    public function resolve(Model $line, DiscountContext $context): Collection;
}
