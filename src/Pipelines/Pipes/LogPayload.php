<?php

namespace PictaStudio\VenditioCore\Pipelines\Pipes;

use Closure;

class LogPayload
{
    public function __invoke(mixed $payload, Closure $next): mixed
    {
        logger()->info('Pipeline input payload', [
            'payload' => $payload,
        ]);

        return $next($payload);
    }
}
