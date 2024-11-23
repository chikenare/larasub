<?php

namespace Err0r\Larasub\Traits;

use Carbon\Carbon;
use Err0r\Larasub\Facades\PlanService;
use Err0r\Larasub\Facades\SubscriptionService;
use Err0r\Larasub\Models\Plan;
use Err0r\Larasub\Models\PlanFeature;
use Err0r\Larasub\Models\Subscription;
use Err0r\Larasub\Models\SubscriptionFeatureUsage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSubscription
{
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(config('larasub.models.subscription'), 'subscriber');
    }

    /**
     * Subscribe the user to a plan.
     *
     * @param  Plan  $plan
     * @return Subscription
     *
     * @throws \InvalidArgumentException
     */
    public function subscribe($plan, ?Carbon $startAt = null, ?Carbon $endAt = null, bool $pending = false)
    {
        /** @var Plan */
        $planClass = config('larasub.models.plan');
        if (! ($plan instanceof $planClass)) {
            throw new \InvalidArgumentException("The plan must be an instance of $planClass");
        }

        $startAt ??= now();

        if ($pending) {
            $startAt = null;
        }

        if ($startAt !== null && $endAt === null && $plan->reset_period !== null && $plan->reset_period_type !== null) {
            $endAt = PlanService::getPlanEndAt($plan, $startAt);
        }

        $subscription = $this->subscriptions()->create([
            'plan_id' => $plan->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);

        return $subscription;
    }

    /**
     * Check if the user is subscribed to a plan.
     *
     * @param  Plan|string  $plan  Plan instance or Plan's ID or slug
     */
    public function subscribed($plan): bool
    {
        return $this
            ->subscriptions()
            ->wherePlan($plan)
            ->where(fn ($q) => $q
                ->active()
                ->orWhere(fn ($q) => $q->pending())
            )
            ->exists();
    }

    /**
     * Get all feature usages for all subscriptions.
     *
     * @return Collection<SubscriptionFeatureUsage>
     */
    public function featuresUsage(): Collection
    {
        $subscriptions = $this->subscriptions()->get();

        return $subscriptions->map(fn ($subscription) => $subscription->featuresUsage()->get())->flatten();
    }

    /**
     * Get the usage of a specific feature for all subscriptions.
     *
     * @return Collection<SubscriptionFeatureUsage>
     */
    public function featureUsage(string $slug): Collection
    {
        $subscriptions = $this->subscriptions()->get();

        return $subscriptions->map(fn ($subscription) => $subscription->featureUsage($slug)->get())->flatten();
    }

    /**
     * Get a specific feature for active subscriptions.
     *
     * @return Collection<PlanFeature>
     */
    public function feature(string $slug): Collection
    {
        $subscriptions = $this->subscriptions()->active()->get();

        return $subscriptions
            ->filter(fn ($subscription) => $subscription->hasFeature($slug))
            ->map(fn ($subscription) => $subscription->feature($slug));
    }

    /**
     * Get feature from active subscriptions that can be used for a specific value.
     *
     * @return Collection<PlanFeature>
     *
     * @throws \InvalidArgumentException
     */
    public function usableFeature(string $slug, float $value)
    {
        $subscriptions = $this->subscriptions()->active()->get();

        return $subscriptions
            ->filter(fn ($subscription) => $subscription->canUseFeature($slug, $value))
            ->map(fn ($subscription) => $subscription->feature($slug));
    }

    /**
     * Check if the model has a specific feature for active subscriptions.
     */
    public function hasActiveFeature(string $slug): bool
    {
        $subscriptions = $this->subscriptions()->active()->get();

        return $subscriptions->some(fn ($subscription) => $subscription->hasActiveFeature($slug));
    }

    /**
     * Get the remaining usage of a specific feature for active subscriptions.
     */
    public function remainingFeatureUsage(string $slug): ?float
    {
        $subscriptions = $this->subscriptions()->active()->get();

        return $subscriptions->map(fn ($subscription) => $subscription->remainingFeatureUsage($slug))->sum();
    }

    /**
     * Get the next time a feature will be available for use
     *
     * @param  string  $slug  The feature slug to check
     * @return \Carbon\Carbon|bool|null
     *
     * @throws \InvalidArgumentException
     *
     * @see \Err0r\Larasub\Facades\SubscriptionService::nextAvailableFeatureUsageBySubscriptions()
     */
    public function nextAvailableFeatureUsage(string $slug)
    {
        $subscriptions = $this->subscriptions()->active()->get();

        return SubscriptionService::nextAvailableFeatureUsageBySubscriptions($subscriptions, $slug);
    }

    /**
     * Check if the model can use a specific feature for active subscriptions.
     */
    public function canUseFeature(string $slug, float $value): bool
    {
        $subscriptions = $this->subscriptions()->active()->get();

        return $subscriptions->some(fn ($subscription) => $subscription->canUseFeature($slug, $value));
    }

    /**
     * Use a specific feature for the first applicable active subscription.
     *
     * @return SubscriptionFeatureUsage
     *
     * @throws \InvalidArgumentException
     */
    public function useFeature(string $slug, float $value)
    {
        $subscriptions = $this->subscriptions()->active()->get();
        $subscription = $subscriptions->first(fn ($subscription) => $subscription->canUseFeature($slug, $value));

        if ($subscription === null) {
            throw new \InvalidArgumentException("No active subscription can use the feature '$slug'");
        }

        return $subscription->useFeature($slug, $value);
    }
}
