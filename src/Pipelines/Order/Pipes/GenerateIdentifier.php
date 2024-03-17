<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Model;
use PictaStudio\VenditioCore\Helpers\Order\Contracts\OrderIdentifierGeneratorInterface;

final class GenerateIdentifier
{
    public function __construct(
        private readonly OrderIdentifierGeneratorInterface $generator,
    ) {
    }

    public function __invoke(Model $order, Closure $next): Model
    {
        $identifier = $this->generator->generate($order);

        $order->fill([
            'identifier' => $identifier,
        ]);

        return $next($order);
    }
}
