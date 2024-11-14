<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Scheduling
    |--------------------------------------------------------------------------
    |
    | Configure automated task scheduling for subscription-related operations.
    | When enabled, the package will automatically fire events for ending and
    | ending-soon subscriptions.
    |
    */

    'scheduling' => [
        'enabled' => env('LARASUB_SCHEDULING_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    |
    | Database table configuration for the subscription system. You can customize
    | table names and UUID settings for each entity. Default names are provided
    | but can be overridden using environment variables.
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
        'events' => [
            'name' => env('LARASUB_TABLE_EVENTS', 'larasub_events'),
            'uuid' => env('LARASUB_TABLE_EVENTS_UUID', true),
        ],
        'eventable' => [
            'uuid' => env('LARASUB_TABLE_EVENTS_EVENTABLE_UUID', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Model class mappings for the subscription system. These classes handle
    | the business logic for plans, features, subscriptions, and related
    | entities. You can extend or replace these with your own implementations.
    |
    */

    'models' => [
        'plan' => \Err0r\Larasub\Models\Plan::class,
        'feature' => \Err0r\Larasub\Models\Feature::class,
        'subscription' => \Err0r\Larasub\Models\Subscription::class,
        'plan_feature' => \Err0r\Larasub\Models\PlanFeature::class,
        'subscription_feature_usages' => \Err0r\Larasub\Models\SubscriptionFeatureUsage::class,
        'event' => \Err0r\Larasub\Models\Event::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Configure the available languages for the subscription system. Define
    | which locales are active and available for translation. Default locales
    | are Arabic (ar) and English (en).
    |
    */

    'localization' => [
        'active' => str(env('LARASUB_LOCALIZATION_ACTIVE', 'ar,en'))
            ->explode(',')
            ->map(fn ($locale) => trim($locale))
            ->toArray(),
    ],
];
