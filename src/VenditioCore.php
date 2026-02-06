<?php

namespace PictaStudio\VenditioCore;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;

class VenditioCore
{
    public static function configureUsing(Closure $callback): void
    {
        $callback(app('venditio-core'));
    }

    public static function configureRateLimiting(string $prefix): void
    {
        RateLimiter::for($prefix, fn (Request $request) => (
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        ));
    }

    public static function registerPolicies(): void
    {
        $models = config('venditio-core.models', []);

        foreach ($models as $key => $modelClass) {
            $model = class_basename($modelClass);
            $policyPath = str($modelClass)->replace('Models', 'Policies')->append('Policy')->toString();

            if (!class_exists($policyPath)) {
                $policyPath = "PictaStudio\VenditioCore\\Policies\\{$model}Policy";

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
