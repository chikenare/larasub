<?php

namespace Err0r\Larasub\Database\Seeders;

use Err0r\Larasub\Enums\SubscriptionStatus as EnumsSubscriptionStatus;
use Err0r\Larasub\Models\SubscriptionStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Lang;

class SubscriptionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeLocales = collect(config('larasub.localization.active'));
        $records = collect(EnumsSubscriptionStatus::cases())
            ->map(fn ($e) => [
                'slug' => $e->value,
                'name' => $activeLocales->mapWithKeys(fn ($locale) => [
                    $locale => Lang::get("larasub::subscription.status.{$e->value}", [], $locale),
                ])->toArray(),
            ])->toArray();

        foreach ($records as $record) {
            $record['name'] = json_encode($record['name']);
            SubscriptionStatus::upsert($record, ['slug'], collect($record)->keys()->toArray());
        }
    }
}
