<?php

namespace PictaStudio\VenditioCore\Pipelines\Orders\Tasks;

use Closure;
use PictaStudio\VenditioCore\Orders\Contracts\OrderIdentifierGeneratorInterface;

final class GenerateIdentifier
{
    public function __construct(
        private readonly OrderIdentifierGeneratorInterface $generator,
    ) {
    }

    public function __invoke(object $payload, Closure $next): mixed
    {
        $this->generator->generate($payload);

        return $next($payload);
    }
}
