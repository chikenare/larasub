<?php

namespace Err0r\Larasub\Traits;

use Carbon\Carbon;
use Err0r\Larasub\Facades\PlanService;
use Err0r\Larasub\Models\Plan;
use Err0r\Larasub\Models\PlanFeature;
use Err0r\Larasub\Models\Subscription;
use Err0r\Larasub\Models\SubscriptionFeatureUsage;
use Illuminate\Database\Eloquent\Collection;

trait HasSubscription
{
    /**
     * Get all subscriptions for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subscriptions()
    {
        return $this->morphMany(config('larasub.models.subscription'), 'subscriber');
    }

    /**
     * Subscribe the user to a plan.
     *
     * @param  Plan  $plan
     * @param  Carbon|null  $startAt
     * @param  Carbon|null  $endAt
     * @return Subscription
     *
     * @throws \InvalidArgumentException
     */
    public function subscribe($plan, ?Carbon $startAt = null, ?Carbon $endAt = null)
    {
        /** @var Plan */
        $planClass = config('larasub.models.plan');
        if (! ($plan instanceof $planClass)) {
            throw new \InvalidArgumentException("The plan must be an instance of $planClass");
        }

        $startAt ??= Carbon::now();
        if ($endAt == null && $plan->reset_period !== null && $plan->reset_period_type !== null) {
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
     * Get all feature usages for active subscriptions.
     *
     * @return Collection<SubscriptionFeatureUsage>
     */
    public function featuresUsage()
    {
        $subscriptions = $this->subscriptions()->active()->get();
        return $subscriptions->map(fn ($subscription) => $subscription->featuresUsage()->get())->flatten();
    }

    /**
     * Get the usage of a specific feature for active subscriptions.
     *
     * @param  string  $slug
     * @return Collection<SubscriptionFeatureUsage>
     */
    public function featureUsage(string $slug)
    {
        $subscriptions = $this->subscriptions()->active()->get();
        return $subscriptions->map(fn ($subscription) => $subscription->featureUsage($slug)->get())->flatten();
    }

    /**
     * Get a specific feature for active subscriptions.
     *
     * @param  string  $slug
     * @return Collection<PlanFeature>
     */
    public function feature(string $slug)
    {
        $subscriptions = $this->subscriptions()->active()->get();
        return $subscriptions->filter(fn ($subscription) => $subscription->hasFeature($slug))->map(fn ($subscription) => $subscription->feature($slug));
    }

    /**
     * Check if the model has a specific feature.
     *
     * @param  string  $slug
     * @return bool
     */
    public function hasFeature(string $slug): bool
    {
        $subscriptions = $this->subscriptions()->active()->get();
        return $subscriptions->some(fn ($subscription) => $subscription->hasFeature($slug));
    }

    /**
     * Get the remaining usage of a specific feature for active subscriptions.
     *
     * @param  string  $slug
     * @return int|null
     */
    public function remainingFeatureUsage(string $slug): ?int
    {
        $subscriptions = $this->subscriptions()->active()->get();
        return $subscriptions->map(fn ($subscription) => $subscription->remainingFeatureUsage($slug))->sum();
    }

    /**
     * Check if the model can use a specific feature.
     *
     * @param  string  $slug
     * @param  float  $value
     * @return bool
     */
    public function canUseFeature(string $slug, float $value): bool
    {
        $subscriptions = $this->subscriptions()->active()->get();
        return $subscriptions->some(fn ($subscription) => $subscription->canUseFeature($slug, $value));
    }

    /**
     * Use a specific feature for active subscriptions.
     *
     * @param  string  $slug
     * @param  float  $value
     * @return SubscriptionFeatureUsage
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
