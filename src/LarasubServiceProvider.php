<?php

namespace Err0r\Larasub;

use Err0r\Larasub\Commands\LarasubSeed;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarasubServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('larasub')
            ->hasConfigFile()
            ->hasMigrations([
                'create_plans_table',
                'create_features_table',
                'create_plan_features_table',
                'create_subscriptions_table',
                'create_subscription_feature_usage_table',
            ])
            ->hasCommand(LarasubSeed::class)
            ->hasTranslations();
    }
}
