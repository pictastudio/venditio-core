<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Dto\Contracts\CartDtoContract;

class FillUserDetails
{
    public function __invoke(CartDtoContract $cartDto, Closure $next): Model
    {
        $cart = $cartDto->getCart()->updateTimestamps();

        $cart->fill([
            'user_id' => $cartDto->getUserId(),
            'user_first_name' => $cartDto->getUserFirstName(),
            'user_last_name' => $cartDto->getUserLastName(),
            'user_email' => $cartDto->getUserEmail(),
            'discount_ref' => $cartDto->getDiscountRef(),
            'addresses' => [
                'billing' => $cartDto->getBillingAddress(),
                'shipping' => $cartDto->getShippingAddress(),
            ],
        ]);

        $cart->setRelation('lines', $cartDto->getLines());

        return $next($cart);
    }
}
