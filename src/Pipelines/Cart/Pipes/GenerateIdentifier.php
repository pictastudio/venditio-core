<?php

namespace PictaStudio\VenditioCore\Pipelines\Cart\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Helpers\Cart\Contracts\CartIdentifierGeneratorInterface;

class GenerateIdentifier
{
    public function __construct(
        private readonly CartIdentifierGeneratorInterface $generator,
    ) {
    }

    public function __invoke(Model $cart, Closure $next): Model
    {
        $identifier = $this->generator->generate($cart);

        $cart->fill([
            'identifier' => $identifier,
        ]);

        return $next($cart);
    }
}
