<?php

namespace Err0r\Larasub\Services;

use Err0r\Larasub\Facades\PeriodService;

final class SubscriptionService
{
    /**
     * @param  \Err0r\Larasub\Models\Subscription  $subscription
     *
     * @throws \InvalidArgumentException
     */
    public function totalFeatureUsageInPeriod($subscription, string $slug): float
    {
        $planFeature = $subscription->plan->feature($slug)->first();

        if ($planFeature === null) {
            throw new \InvalidArgumentException("The feature '$slug' is not part of the plan");
        }

        $usages = $subscription->featureUsage($slug);
        if ($planFeature->reset_period !== null && $planFeature->reset_period_type !== null) {
            $resetPeriod = $planFeature->reset_period;
            $resetPeriodType = $planFeature->reset_period_type;
            $resetMinutes = PeriodService::getMinutes($resetPeriod, $resetPeriodType);
            $usages = $usages->where('created_at', '>=', now()->subMinutes($resetMinutes));
        }

        return $usages->sum('value');
    }
}
