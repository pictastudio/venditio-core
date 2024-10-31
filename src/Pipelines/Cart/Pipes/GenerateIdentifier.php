<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Contracts\CartIdentifierGeneratorInterface;

class GenerateIdentifier
{
    public function __construct(
        private readonly CartIdentifierGeneratorInterface $generator,
    ) {
    }

    public function __invoke(Model $cart, Closure $next): Model
    {
        $cart->fill([
            'identifier' => $this->generator->generate($cart),
        ]);

        return $next($cart);
    }
}
