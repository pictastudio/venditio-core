<?php

namespace PictaStudio\Venditio\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

class InstallCommand extends Command
{
    protected $signature = 'venditio:install';

    protected $description = 'Install Venditio package';

    public function handle(): int
    {
        $this->components->info('Installing Venditio package...');

        $this->components->info('Publishing venditio configuration...');
        $this->call('vendor:publish', ['--tag' => 'venditio-config']);

        $this->components->info('Publishing venditio migrations...');
        $this->call('vendor:publish', ['--tag' => 'venditio-migrations']);

        if (confirm('Do you want to publish bruno api files?', false)) {
            $this->components->info('Publishing bruno api files...');
            $this->call('vendor:publish', ['--tag' => 'venditio-bruno']);
        }

        if (confirm('Do you want to run migrations now?')) {
            $this->call('migrate');
        }

        $this->components->info('Venditio package installed successfully.');

        return self::SUCCESS;
    }
}
