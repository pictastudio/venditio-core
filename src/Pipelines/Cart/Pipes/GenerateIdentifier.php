<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use PictaStudio\VenditioCore\Helpers\Cart\Contracts\CartIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Models\Cart;

class GenerateIdentifier
{
    public function __construct(
        private readonly CartIdentifierGeneratorInterface $generator,
    ) {
    }

    public function __invoke(Cart $cart, Closure $next): Cart
    {
        $identifier = $this->generator->generate($cart);

        $cart->fill([
            'identifier' => $identifier,
        ]);

        return $next($cart);
    }
}
