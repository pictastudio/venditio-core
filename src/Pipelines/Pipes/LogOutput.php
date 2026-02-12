<?php

namespace PictaStudio\VenditioCore\Pipelines\Pipes;

use Closure;

class LogOutput
{
    public function __invoke(mixed $payload, Closure $next): mixed
    {
        $response = $next($payload);

        logger()->info('Pipeline output', [
            'payload' => $payload,
            'response' => $response,
        ]);

        return $response;
    }
}
