<?php

namespace PictaStudio\VenditioCore\Packages\Tools\Commands;

use Closure;
use Illuminate\Support\Str;
use PictaStudio\VenditioCore\Packages\Tools\Package;
use Spatie\LaravelPackageTools\Commands\InstallCommand as SpatieInstallCommand;
use Spatie\LaravelPackageTools\Package as SpatiePackage;

use function Laravel\Prompts\confirm;

class InstallCommand extends SpatieInstallCommand
{
    /**
     * @var Package the variable type definition must remain the SpatiePackage, the docblock serves to provide autocomplete inside IDE
     */
    protected SpatiePackage $package;

    public ?Closure $startWith = null;

    protected array $publishes = [];

    protected bool $askToRunMigrations = false;

    protected bool $copyServiceProviderInApp = false;

    protected ?string $starRepo = null;

    public ?Closure $endWith = null;

    public $hidden = true;

    public function __construct(SpatiePackage $package)
    {
        parent::__construct($package);
    }

    public function handle()
    {
        $this->package->registerMigrations(true);

        $this->copyMigrationsToApp();

        if ($this->startWith) {
            ($this->startWith)($this);
        }

        foreach ($this->publishes as $tag) {
            $name = str_replace('-', ' ', $tag);
            $this->components->info("Publishing {$name}...");

            $this->callSilently('vendor:publish', [
                '--tag' => "{$this->package->shortName()}-{$tag}",
            ]);
        }

        if ($this->askToRunMigrations) {
            if (confirm('Would you like to run the migrations now?')) {
                $this->components->info('Running migrations...');

                $this->call('migrate');
            }
        }

        if ($this->copyServiceProviderInApp) {
            $this->components->info('Publishing service provider...');
            
            $this->copyServiceProviderInApp();
        }

        $this->components->info('Publishing spatie/laravel-permission migrations and config...');
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
        ]);

        $this->components->info('Publishing spatie/laravel-activitylog migrations and config...');
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider',
        ]);

        if ($this->starRepo) {
            if (confirm('Would you like to star our repo on GitHub?')) {
                $repoUrl = "https://github.com/{$this->starRepo}";

                if (PHP_OS_FAMILY == 'Darwin') {
                    exec("open {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Windows') {
                    exec("start {$repoUrl}");
                }
                if (PHP_OS_FAMILY == 'Linux') {
                    exec("xdg-open {$repoUrl}");
                }
            }
        }

        $this->components->info("{$this->package->shortName()} has been installed!");

        if ($this->endWith) {
            ($this->endWith)($this);
        }
    }

    public function copyMigrationsToApp(): void
    {
        $this->components->info('Publishing migrations...');

        foreach ($this->package->migrationFileNames as $migration) {
            $appDatabasePath = database_path(
                'migrations/' .
                now()->format('Y_m_d_His') .
                '_' .
                Str::of($migration)->afterLast('Migrations')->snake()->finish('.php')->explode('_')->slice(4)->join('_')
            );

            copy($migration . '.php', $appDatabasePath);
        }
    }

    public function publish(string ...$tag): self
    {
        $this->publishes = array_merge($this->publishes, $tag);

        return $this;
    }

    public function publishConfigFile(): self
    {
        return $this->publish('config');
    }

    public function publishAssets(): self
    {
        return $this->publish('assets');
    }

    public function publishMigrations(): self
    {
        return $this->publish('migrations');
    }

    public function askToRunMigrations(): self
    {
        $this->askToRunMigrations = true;

        return $this;
    }
}
