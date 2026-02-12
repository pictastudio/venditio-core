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

class FillDataFromPayload
{
    public function __invoke(CartDtoContract $cartDto, Closure $next): Model
    {
        $addresses = $cartDto->getAddresses();

        if (blank($addresses)) {
            /** @var BackedEnum $addressTypeEnum */
            $addressTypeEnum = config('venditio-core.addresses.type_enum');

            foreach ($addressTypeEnum::cases() as $case) {
                $addresses[$case->value] = [];
            }
        }

        $cart = $cartDto->toModel();

        $cart->fill([
            'status' => resolve_enum('cart_status')::getProcessingStatus(),
            'addresses' => $addresses,
        ]);

        if (!$cart->exists || $cartDto->hasLinesProvided()) {
            $cart->setRelation('lines', $cartDto->getLines());
        }

        $cart->setAttribute('lines_payload_provided', $cartDto->hasLinesProvided());

        return $next($cart);
    }
}
