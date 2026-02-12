<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use BackedEnum;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;
use PictaStudio\VenditioCore\Pipelines\CartLine\CartLineCreationPipeline;

use function PictaStudio\VenditioCore\Helpers\Functions\get_fresh_model_instance;
use function PictaStudio\VenditioCore\Helpers\Functions\resolve_enum;

class CalculateShippingFees
{
    public function __invoke(Model $cart, Closure $next): Model
    {
        return $next($cart);
    }
}
