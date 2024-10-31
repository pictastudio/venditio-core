<?php

namespace PictaStudio\VenditioCore\Packages\Tools;

enum PackageType: string
{
    case Simple = 'simple';
    case Advanced = 'advanced';

    public function getPath(?string $subFolder = null, ?string $directory = null): string
    {
        if (filled($subFolder)) {
            $subFolder = match ($subFolder) {
                'commands' => 'Console/Commands',
                'config' => 'Config',
                'database' => 'Database',
                'migrations' => 'Database/Migrations',
                'seeders' => 'Database/Seeders',
                'resources' => 'Resources',
                'routes' => 'Routes',
            };
        }

        $path = match ($this) {
            self::Simple => 'Packages/Simple',
            self::Advanced => 'Packages/Advanced',
        };

        return $path . ($subFolder ? '/' . $subFolder : '') . ($directory ? '/' . $directory : '');
    }

    public function getMigrations(): array
    {
        return collect(scandir($this->getPath('migrations')))
            ->reject(fn (string $file) => in_array($file, ['.', '..']))
            ->map(fn (string $file) => str($file)->beforeLast('.php')->prepend($migrationsBasePath . '/')->toString())
            ->toArray();
    }
}
