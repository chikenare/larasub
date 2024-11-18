# Laravel Subscription Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/err0r/larasub.svg?style=flat-square)](https://packagist.org/packages/err0r/larasub)
[![Total Downloads](https://img.shields.io/packagist/dt/err0r/larasub.svg?style=flat-square)](https://packagist.org/packages/err0r/larasub)

A powerful and flexible subscription management system for Laravel applications that provides:

✨ **Core Features**
- 📦 Subscription Plans with tiered pricing
- 🔄 Flexible billing periods (minute/hour/day/week/month/year)
- 🎯 Feature-based access control
- 📊 Usage tracking and limits
- 🔋 Consumable and non-consumable features

⚡ **Key Capabilities**
- 💳 Subscribe users to plans with custom periods
- 📈 Track feature usage and quotas
- ⏰ Built-in subscription lifecycle events
- 🔄 Cancel/Resume subscription workflows
- 📅 Period-based feature resets
- 🌐 Multi-language support (translatable plans/features)
- 🔍 Feature usage monitoring
- 🎚️ Customizable usage limits

🛠️ **Developer Experience**
- 🧩 Simple trait-based integration
- ⚙️ Configurable tables and models
- 📝 Comprehensive event system
- 🔌 UUID support out of the box

## Installation

You can install the package via composer:

```bash
composer require err0r/larasub
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="larasub-config"
```

Publish and run the migrations with:

```bash
php artisan vendor:publish --tag="larasub-migrations"
php artisan migrate
```

## Basic Usage

1. **Setup the Subscriber Model**  
   Add the `HasSubscription` trait to your model:

    ```php
    <?php
    use Err0r\Larasub\Traits\HasSubscription;

    class User extends Model
    {
        use HasSubscription;
    }
    ```

2. **Create a Feature**

    ```php
    <?php
    use Err0r\Larasub\Builders\FeatureBuilder;

    // Create a new feature
    $apiCalls = FeatureBuilder::create('api-calls')
        ->name(['en' => 'API Calls', 'ar' => 'مكالمات API'])
        ->description(['en' => 'Number of API calls allowed', 'ar' => 'عدد المكالمات المسموح به'])
        ->consumable()
        ->sortOrder(1)
        ->build();

    $apiCallPriority = FeatureBuilder::create('api-call-priority')
        ->name(['en' => 'API Calls Priority', 'ar' => 'الاولوية في مكالمات الـ API'])
        ->description(['en' => 'Priority access to API calls', 'ar' => 'الوصول بأولوية إلى مكالمات الـ API'])
        ->nonConsumable()
        ->sortOrder(2)
        ->build();
    ```

3. **Create a Plan**
    
    Create subscription plans using the `PlanBuilder` class. When configuring a plan's features, you can specify:

    - Feature values and display names
    - Consumption mode (consumable vs non-consumable)
    - Reset intervals (periodic vs fixed)
    - Additional feature properties

    ```php
    <?php
    use Err0r\Larasub\Builders\PlanBuilder;
    use Err0r\Larasub\Enums\Period;
    use Err0r\Larasub\Enums\FeatureValue;

    // Create a new plan
    $plan = PlanBuilder::create('premium')
        ->name(['en' => 'Premium Plan', 'ar' => 'خطة مميزة'])
        ->description(['en' => 'Access to premium features', 'ar' => 'الوصول إلى الميزات المميزة'])
        ->price(99.99, 'USD')
        ->resetPeriod(1, Period::MONTH)
        ->addFeature('api-calls', fn ($feature) => $feature
            ->value(1000)
            ->resetPeriod(1, Period::DAY)
            ->displayValue(['en' => '1000 API Calls', 'ar' => '1000 مكالمة API'])
            ->sortOrder(1);
        )
        ->addFeature('download-requests', fn ($feature) => $feature
            ->value(FeatureValue::UNLIMITED)
            ->displayValue(['en' => 'Unlimited Download Requests', 'ar' => 'طلبات تنزيل غير محدودة'])
            ->sortOrder(2);
        )
        ->addFeature('api-call-priority', fn ($feature) => $feature
            ->value('high')
            ->displayValue(['en' => 'High Priority API Calls', 'ar' => 'مكالمات API ذات أولوية عالية'])
            ->sortOrder(3);
        )
        ->build();
    ```

4. **Create a Subscription**

    ```php
    <?php
    // Get a plan
    $plan = Plan::slug('basic')->first();

    // Subscribe user to the plan
    $user->subscribe($plan);

    // Subscribe with custom dates
    $user->subscribe($plan, 
        startAt: now(), 
        endAt: now()->addYear()
    );
    ```

5. **Check Subscription Status**

    ```php
    <?php
    $subscription = $user->subscriptions()->first();

    // Check if subscription is active
    $subscription->isActive();

    // Check if subscription is cancelled
    $subscription->isCancelled();

    // Check if subscription has expired
    $subscription->isExpired();
    ```

6. **Feature Management**

    ```php
    <?php
    // Check if user has a feature
    $user->hasFeature('unlimited-storage');

    // Check if user can use a feature
    $user->canUseFeature('api-calls', 1);

    // Track feature usage
    $user->useFeature('api-calls', 1);

    // Check remaining feature usage
    $user->remainingFeatureUsage('api-calls');
    ```

## Advanced Usage

1. **Subscription Management**

    ```php
    <?php
    // Get all active subscriptions
    $user->subscriptions()->active()->get();

    // Cancel a subscription
    $subscription->cancel();

    // Cancel immediately (ends subscription now)
    $subscription->cancel(immediately: true);

    // Resume a cancelled subscription
    $subscription->resume(
        startAt: now(),
        endAt: now()->addMonth() // Optional. Default: Subscription Start Date + Plan Duration
    );
    ```

2. **Feature Types & Usage**   

    Feature methods can be called in two ways:
    - On a specific subscription model - Returns results for just that subscription
        ```php
        <?php
        use Err0r\Larasub\Enums\FeatureType;

        $subscription = $user->subscriptions()->active()->first();

        if ($subscription->canUseFeature('download-requests', 1)) {
            // User can make 1 API calls on this subscription
        }

        // Get a collection of SubscriptionFeatureUsage for a specific feature on a specific subscription
        $usage = $subscription->featureUsage('download-requests');

        // Use a feature on a specific subscription
        $subscription->useFeature('download-requests', 1);

        // Get remaining usage on a specific subscription
        $remaining = $subscription->remainingFeatureUsage('download-requests');

        // Check if feature exists on a specific subscription
        $hasFeature = $subscription->hasFeature('platinum-support');
        ```

    - On the subscriber model - Returns results aggregated across all active subscriptions
        ```php
        <?php
        use Err0r\Larasub\Enums\FeatureType;

        // Check feature usage limit
        if ($user->canUseFeature('api-calls', 5)) {
            // User can make 5 API calls
        }

        // Get a collection of SubscriptionFeatureUsage for a specific feature across all user's subscriptions
        $usage = $user->featureUsage('api-calls');

        // Use the feature from the first active and applicable subscription
        $user->useFeature('api-calls', 5);

        // Check if feature exists on any active subscription
        $hasFeature = $user->hasFeature('premium-support');

        // Get remaining usage across all active subscriptions
        $remaining = $user->remainingFeatureUsage('api-calls');
        ```

3. **Events**

    The package dispatches events for subscription lifecycle:
    - `SubscriptionEnded` - When a subscription expires
    - `SubscriptionEndingSoon` - When a subscription is ending soon (configurable in `larasub.php`. Default: 7 days)

    > By default, the package includes a task schedule that runs every minute to check for subscriptions that have ended or are ending soon, and triggers the corresponding events.   
    > You can modify this schedule in the `larasub.php` configuration file.

    **Event Listener Example:**
    ```php
    <?php

    namespace App\Listeners;

    use Err0r\Larasub\Events\SubscriptionEnded;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Support\Facades\Log;

    class HandleEndedSubscription
    {
        /**
         * Handle the event.
         */
        public function handle(SubscriptionEnded $event): void
        {
            // Handle subscription ending
        }
    }
    ```

## Resource Classes

The package provides several resource classes to transform your models into JSON representations:

- [`FeatureResource`](src/Resources/FeatureResource.php): Transforms a feature model.
- [`PlanResource`](src/Resources/PlanResource.php): Transforms a plan model.
- [`PlanFeatureResource`](src/Resources/PlanFeatureResource.php): Transforms a plan feature model.
- [`SubscriptionResource`](src/Resources/SubscriptionResource.php): Transforms a subscription model.
- [`SubscriptionFeatureUsageResource`](src/Resources/SubscriptionFeatureUsageResource.php): Transforms a subscription feature usage model.

## Testing
> TODO   

```bash
composer test
```

## Changelog
> TODO   

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing
> TODO   

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities
> TODO   

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Faisal](https://github.com/err0r)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.