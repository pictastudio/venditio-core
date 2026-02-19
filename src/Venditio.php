<?php

namespace PictaStudio\Venditio;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class Venditio
{
    public static function configureUsing(Closure $callback): void
    {
        $callback(app('venditio'));
    }

    public static function configureRateLimiting(string $prefix): void
    {
        RateLimiter::for($prefix, fn (Request $request) => (
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        ));
    }
}
