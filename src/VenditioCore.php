<?php

namespace PictaStudio\VenditioCore;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Gate, RateLimiter};
use PictaStudio\VenditioCore\Exceptions\MissingPackageTypeConfiguration;
use PictaStudio\VenditioCore\Facades\VenditioCore as VenditioCoreFacade;
use PictaStudio\VenditioCore\Packages\Tools\PackageType;

class VenditioCore
{
    public PackageType $packageType = PackageType::Simple;

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
        $packageType = VenditioCoreFacade::getPackageType();

        // defaults to simple package but can be overridden by the advanced package
        $models = array_merge(
            config('venditio-core.models.simple'),
            config('venditio-core.models.' . $packageType->value),
        );

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

    public function packageType(PackageType $packageType): static
    {
        $this->packageType = $packageType;

        return $this;
    }

    public function getPackageType(): PackageType
    {
        // if (blank($this->packageType)) {
        //     throw new MissingPackageTypeConfiguration('No package type has been configured. Please configure a package type using the [packageType] method inside a service provider (see docs for reference https://github.com/pictastudio/venditio-core).');
        // }

        // dd(getenv('COMPOSER_COMMAND'));

        throw_if(
            blank($this->packageType),
            MissingPackageTypeConfiguration::class,
            'No package type has been configured. Please configure a package type using the [PictaStudio\VenditioCore\Facades\VenditioCore::packageType] method inside a service provider (see docs for reference https://github.com/pictastudio/venditio-core).',
        );

        return $this->packageType;
    }

    public function isSimple(): bool
    {
        return $this->getPackageType() === PackageType::Simple;
    }

    public function isAdvanced(): bool
    {
        return $this->getPackageType() === PackageType::Advanced;
    }
}
