<?php

namespace PictaStudio\VenditioCore\Packages\Tools;

use Carbon\{Carbon, CarbonImmutable};
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\{Package as SpatiePackage, PackageServiceProvider as LaravelPackageToolsPackageServiceProvider};

abstract class PackageServiceProvider extends LaravelPackageToolsPackageServiceProvider
{
    /**
     * @var Package the variable type definition must remain the SpatiePackage, the docblock serves to provide autocomplete inside IDE
     */
    protected SpatiePackage $package;

    abstract public function configurePackage(SpatiePackage $package): void;

    public function register()
    {
        $this->registeringPackage();

        $this->package = $this->newPackage();

        $this->package->setBasePath($this->getPackageBaseDir());

        $this->configurePackage($this->package);

        if (empty($this->package->name)) {
            throw InvalidPackage::nameIsRequired();
        }

        foreach ($this->package->configFileNames as $configFileName) {
            $this->mergeConfigFrom($this->package->basePath("/../config/{$configFileName}.php"), $configFileName);
        }

        $this->packageRegistered();

        return $this;
    }

    public function newPackage(): SpatiePackage
    {
        return new SpatiePackage;
    }

    public function boot()
    {
        $this->bootingPackage();

        if ($this->package->hasTranslations) {
            $langPath = 'vendor/' . $this->package->shortName();

            $langPath = (function_exists('lang_path'))
                ? lang_path($langPath)
                : resource_path('lang/' . $langPath);
        }

        if ($this->app->runningInConsole()) {
            foreach ($this->package->configFileNames as $configFileName) {
                $this->publishes([
                    $this->package->basePath("/../config/{$configFileName}.php") => config_path("{$configFileName}.php"),
                ], "{$this->package->shortName()}-config");
            }

            if ($this->package->hasViews) {
                $this->publishes([
                    $this->package->basePath('/../resources/views') => base_path("resources/views/vendor/{$this->packageView($this->package->viewNamespace)}"),
                ], "{$this->packageView($this->package->viewNamespace)}-views");
            }

            if ($this->package->hasInertiaComponents) {
                $packageDirectoryName = Str::of($this->packageView($this->package->viewNamespace))->studly()->remove('-')->value();

                $this->publishes([
                    $this->package->basePath('/../resources/js/Pages') => base_path("resources/js/Pages/{$packageDirectoryName}"),
                ], "{$this->packageView($this->package->viewNamespace)}-inertia-components");
            }

            // dd($this->package->migrationFileNames);

            $now = Carbon::now();
            foreach ($this->package->migrationFileNames as $migrationFileName) {
                $filePath = $migrationFileName . '.php';
                // dd($migrationFileName);
                // $filePath = $this->package->getPackageTypeBasePath('migrations', "{$migrationFileName}.php");
                // dd($filePath);

                // $filePath = $this->package->basePath("/../database/migrations/{$migrationFileName}.php");
                // dd($filePath);
                if (!file_exists($filePath)) {
                    // Support for the .stub file extension
                    $filePath .= '.stub';
                }

                // dd([
                //     $filePath => $this->generateMigrationName(
                //         $migrationFileName,
                //         $now->addSecond()
                //     )
                // ]);
                $this->publishes([
                    $filePath => $this->generateMigrationName(
                        $migrationFileName,
                        $now->addSecond()
                    ), ], "{$this->package->shortName()}-migrations");

                if ($this->package->runsMigrations) {
                    $this->loadMigrationsFrom($filePath);
                }
            }

            if ($this->package->hasTranslations) {
                $this->publishes([
                    $this->package->basePath('/../resources/lang') => $langPath,
                ], "{$this->package->shortName()}-translations");
            }

            if ($this->package->hasAssets) {
                $this->publishes([
                    $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
                ], "{$this->package->shortName()}-assets");
            }
        }

        if (!empty($this->package->commands)) {
            $this->commands($this->package->commands);
        }

        if (!empty($this->package->consoleCommands) && $this->app->runningInConsole()) {
            $this->commands($this->package->consoleCommands);
        }

        if ($this->package->hasTranslations) {
            $this->loadTranslationsFrom(
                $this->package->basePath('/../resources/lang/'),
                $this->package->shortName()
            );

            $this->loadJsonTranslationsFrom($this->package->basePath('/../resources/lang/'));

            $this->loadJsonTranslationsFrom($langPath);
        }

        if ($this->package->hasViews) {
            $this->loadViewsFrom($this->package->basePath('/../resources/views'), $this->package->viewNamespace());
        }

        foreach ($this->package->viewComponents as $componentClass => $prefix) {
            $this->loadViewComponentsAs($prefix, [$componentClass]);
        }

        if (count($this->package->viewComponents)) {
            $this->publishes([
                $this->package->basePath('/Components') => base_path("app/View/Components/vendor/{$this->package->shortName()}"),
            ], "{$this->package->name}-components");
        }

        if ($this->package->publishableProviderName) {
            $this->publishes([
                $this->package->basePath("/../resources/stubs/{$this->package->publishableProviderName}.php.stub") => base_path("app/Providers/{$this->package->publishableProviderName}.php"),
            ], "{$this->package->shortName()}-provider");
        }

        foreach ($this->package->routeFileNames as $routeFileName) {
            $this->loadRoutesFrom("{$this->package->basePath('/../routes/')}{$routeFileName}.php");
        }

        foreach ($this->package->sharedViewData as $name => $value) {
            View::share($name, $value);
        }

        foreach ($this->package->viewComposers as $viewName => $viewComposer) {
            View::composer($viewName, $viewComposer);
        }

        $this->packageBooted();

        return $this;
    }

    public function registeringPackage() {}

    public function packageRegistered() {}

    public function bootingPackage() {}

    public function packageBooted() {}

    public function packageView(?string $namespace): ?string
    {
        return $namespace === null
            ? $this->package->shortName()
            : $this->package->viewNamespace;
    }

    protected function generateMigrationName(string $migrationFileName, Carbon|CarbonImmutable $now): string
    {
        $migrationsPath = dirname($migrationFileName) . '/';
        $migrationFileName = basename($migrationFileName);
        // dd($migrationsPath, $migrationFileName);

        $len = mb_strlen($migrationFileName) + 4;

        if (Str::contains($migrationFileName, '/')) {
            $migrationsPath .= Str::of($migrationFileName)->beforeLast('/')->finish('/');
            $migrationFileName = Str::of($migrationFileName)->afterLast('/');
        }

        foreach (glob(database_path("{$migrationsPath}*.php")) as $filename) {
            if ((mb_substr($filename, -$len) === $migrationFileName . '.php')) {
                return $filename;
            }
        }

        // dd(
        //     Str::of($migrationFileName)->snake()->finish('.php'),
        //     Str::of($migrationFileName)->snake()->finish('.php')->explode('_')->slice(4)->join('_'),
        // );
        // dd($migrationsPath . $now->format('Y_m_d_His') . '_' . Str::of($migrationFileName)->snake()->finish('.php')->explode('_')->slice(4)->join('_'));
        return database_path(
            'migrations/' .
            $now->format('Y_m_d_His') .
            '_' .
            Str::of($migrationFileName)->snake()->finish('.php')->explode('_')->slice(4)->join('_')
        );
        // dd($migrationsPath . Str::of($migrationFileName)->snake()->finish('.php'));

        return database_path($migrationsPath . $now->format('Y_m_d_His') . '_' . Str::of($migrationFileName)->snake()->finish('.php'));
    }

    protected function getPackageBaseDir(): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName());
    }
}
