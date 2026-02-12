<?php

namespace PictaStudio\Venditio;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Gate, RateLimiter};

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

    public static function registerPolicies(): void
    {
        $models = config('venditio.models', []);

        foreach ($models as $key => $modelClass) {
            $model = class_basename($modelClass);
            $policyPath = str($modelClass)->replace('Models', 'Policies')->append('Policy')->toString();

            if (!class_exists($policyPath)) {
                $policyPath = "PictaStudio\Venditio\Policies\\{$model}Policy";

                if (!class_exists($policyPath)) {
                    continue;
                }
            }

            Gate::policy(
                $modelClass,
                $policyPath,
            );
        }
    }
}
