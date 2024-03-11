<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use PictaStudio\VenditioCore\Dto\CartDto;
use PictaStudio\VenditioCore\Models\Cart;

class FillUserDetails
{
    public function __invoke(CartDto $cartDto, Closure $next): Cart
    {
        $cart = $cartDto->getCart();

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
