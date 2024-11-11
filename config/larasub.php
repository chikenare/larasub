<?php

return [
    /*
  |--------------------------------------------------------------------------
  | Tables
  |--------------------------------------------------------------------------
  | Tables ...
  |
  */

    'tables' => [
        'users' => [
            'name' => env('LARASUB_TABLE_USERS', 'users'),
            'uuid' => env('LARASUB_TABLE_USERS_UUID', default: false),
        ],
        'plans' => [
            'name' => env('LARASUB_TABLE_PLANS', 'plans'),
            'uuid' => env('LARASUB_TABLE_PLANS_UUID', true),
        ],
        'features' => [
            'name' => env('LARASUB_TABLE_FEATURES', 'features'),
            'uuid' => env('LARASUB_TABLE_FEATURES_UUID', true),
        ],
        'subscriptions' => [
            'name' => env('LARASUB_TABLE_SUBSCRIPTIONS', 'subscriptions'),
            'uuid' => env('LARASUB_TABLE_SUBSCRIPTIONS_UUID', true),
        ],
        'plan_features' => [
            'name' => env('LARASUB_TABLE_PLANS_FEATURES', 'plan_features'),
        ],
        'subscription_feature_usage' => [
            'name' => env('LARASUB_TABLE_SUBSCRIPTION_FEATURE_USAGE', 'subscription_feature_usage'),
        ],
    ],

    'models' => [
        'plan' => \Err0r\Larasub\Models\Plan::class,
        'feature' => \Err0r\Larasub\Models\Feature::class,
        'subscription' => \Err0r\Larasub\Models\Subscription::class,
    ],

    'localization' => [
        'active' => str(env('LARASUB_LOCALIZATION_ACTIVE', 'ar,en'))
            ->explode(',')
            ->map(fn ($locale) => trim($locale))
            ->toArray(),
    ],
];
