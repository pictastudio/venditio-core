<?php

namespace PictaStudio\VenditioCore\Pipelines\Order\Pipes;

use Closure;
use PictaStudio\VenditioCore\Helpers\Order\Contracts\OrderIdentifierGeneratorInterface;
use PictaStudio\VenditioCore\Models\Order;

final class GenerateIdentifier
{
    public function __construct(
        private readonly OrderIdentifierGeneratorInterface $generator,
    ) {
    }

    public function __invoke(Order $order, Closure $next): Order
    {
        $identifier = $this->generator->generate($order);

        $order->fill([
            'identifier' => $identifier,
        ]);

        return $next($order);
    }
}
