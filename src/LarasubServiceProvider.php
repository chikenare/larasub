<?php

namespace Err0r\Larasub;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Err0r\Larasub\Commands\LarasubCommand;

class LarasubServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('larasub')
            ->hasConfigFile()
            ->hasMigration('create_larasub_table')
            ->hasCommand(LarasubCommand::class);
    }
}
