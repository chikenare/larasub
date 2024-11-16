# Laravel Subscription Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/err0r/larasub.svg?style=flat-square)](https://packagist.org/packages/err0r/larasub)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/err0r/larasub/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/err0r/larasub/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/err0r/larasub/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/err0r/larasub/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
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
    $feature = FeatureBuilder::create('api-calls')
        ->name(['en' => 'API Calls', 'ar' => 'مكالمات API'])
        ->description(['en' => 'Number of API calls allowed', 'ar' => 'عدد المكالمات المسموح به'])
        ->consumable()
        ->sortOrder(1)
        ->build();
    ```

3. **Create a Plan**

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
        ->addFeature('api-calls', function ($feature) {
            $feature->value(1000)
                    ->displayValue(['en' => '1000 API Calls', 'ar' => '1000 مكالمة API'])
                    ->sortOrder(1);
        })
        ->addFeature('premium-support', function ($feature) {
            $feature->value(FeatureValue::UNLIMITED)
                    ->displayValue(['en' => 'Unlimited Premium Support', 'ar' => 'دعم مميز غير محدود'])
                    ->sortOrder(2);
        })
        ->build();
    ```

4. **Create a Subscription**

    ```php
    <?php
    // Get a plan
    $plan = Plan::where('slug', 'basic')->first();

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

    ```php
    <?php
    use Err0r\Larasub\Enums\FeatureType;

    // Check feature usage limit
    if ($user->canUseFeature('api-calls', 5)) {
        // User can make 5 API calls
    }

    // Get a collection of SubscriptionFeatureUsage for a specific feature across all user's subscriptions
    $usage = $user->featureUsage('api-calls');

    // Check if feature exists on any active subscription
    $hasFeature = $user->hasFeature('premium-support');

    // Get remaining usage across all active subscriptions
    $remaining = $user->remainingFeatureUsage('api-calls');
    ```

3. **Events**

    The package dispatches events for subscription lifecycle:
    - `SubscriptionEnded` - When a subscription expires
    - `SubscriptionEndingSoon` - 24 hours before expiration

    > By default, the package includes a task schedule that runs every minute to check for subscriptions that have ended or are ending soon, and triggers the corresponding events.   
    > You can modify this schedule in the `larasub.php` configuration file.

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