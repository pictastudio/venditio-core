<?php

namespace PictaStudio\VenditioCore\Pipelines\CartLine\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\DiscountCalculatorInterface;
use PictaStudio\VenditioCore\Discounts\DiscountContext;

use function PictaStudio\VenditioCore\Helpers\Functions\query;

class ApplyDiscount
{
    public function __construct(
        private readonly DiscountCalculatorInterface $discountCalculator,
    ) {}

    public function __invoke(Model $cartLine, Closure $next): Model
    {
        $cart = $cartLine->relationLoaded('cart') && $cartLine->cart instanceof Model
            ? $cartLine->cart
            : $this->resolveCart($cartLine);

        $user = $cart?->relationLoaded('user') && $cart->user instanceof Model
            ? $cart->user
            : (filled($cart?->getAttribute('user_id'))
                ? query('user')->find($cart->getAttribute('user_id'))
                : null);

        $context = DiscountContext::make(
            cart: $cart,
            user: $user,
        );

        $this->discountCalculator->apply($cartLine, $context);

        return $next($cartLine);
    }

    private function resolveCart(Model $cartLine): ?Model
    {
        $cartId = $cartLine->getAttribute('cart_id');

        if (blank($cartId)) {
            return null;
        }

        return query('cart')->find($cartId);
    }
}
