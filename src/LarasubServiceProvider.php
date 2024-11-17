<?php

namespace Err0r\Larasub;

use Err0r\Larasub\Commands\CheckEndingSubscriptions;
use Err0r\Larasub\Commands\LarasubSeed;
use Illuminate\Console\Scheduling\Schedule;
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
                'create_events_table',
            ])
            // ->hasTranslations()
            ->hasCommand(LarasubSeed::class)
            ->hasCommand(CheckEndingSubscriptions::class);
    }

    public function packageBooted(): void
    {
        $this->app->booted(function () {
            /** @var Schedule */
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('larasub:check-ending-subscriptions')
                ->everyMinute()
                ->withoutOverlapping()
                // ->sendOutputTo(storage_path('logs/larasub-check-ending-subscriptions.log'), true)
                ->when(config('larasub.scheduling.enabled'));
        });
    }
}
