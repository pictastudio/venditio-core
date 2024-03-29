<?php

namespace PictaStudio\VenditioCore;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;

class VenditioCore
{
    public static function configureRateLimiting(string $prefix): void
    {
        RateLimiter::for($prefix, fn (Request $request) => (
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        ));
    }

    public static function registerPolicies(): void
    {
        foreach (config('venditio-core.models') as $contract => $model) {
            $model = class_basename($model);

            if (!class_exists("PictaStudio\VenditioCore\\Policies\\{$model}Policy")) {
                continue;
            }

            Gate::policy(
                "PictaStudio\VenditioCore\Models\\{$model}",
                "PictaStudio\VenditioCore\Policies\\{$model}Policy"
            );
        }
    }
}
