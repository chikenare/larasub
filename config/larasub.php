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
        'subscribers' => [
            'uuid' => env('LARASUB_TABLE_SUBSCRIBERS_UUID', default: false),
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
            'uuid' => env('LARASUB_TABLE_PLANS_FEATURES_UUID', true),
        ],
        'subscription_feature_usages' => [
            'name' => env('LARASUB_TABLE_SUBSCRIPTION_FEATURE_USAGES', 'subscription_feature_usages'),
            'uuid' => env('LARASUB_TABLE_SUBSCRIPTION_FEATURE_USAGES_UUID', true),
        ],
    ],

    'models' => [
        'plan' => \Err0r\Larasub\Models\Plan::class,
        'feature' => \Err0r\Larasub\Models\Feature::class,
        'subscription' => \Err0r\Larasub\Models\Subscription::class,
        'plan_feature' => \Err0r\Larasub\Models\PlanFeature::class,
        'subscription_feature_usages' => \Err0r\Larasub\Models\SubscriptionFeatureUsage::class,
    ],

    'localization' => [
        'active' => str(env('LARASUB_LOCALIZATION_ACTIVE', 'ar,en'))
            ->explode(',')
            ->map(fn ($locale) => trim($locale))
            ->toArray(),
    ],
];
