<?php

namespace Err0r\Larasub\Traits;

use Carbon\Carbon;
use Err0r\Larasub\Enums\SubscriptionStatus;
use Err0r\Larasub\Facades\PlanService;
use Err0r\Larasub\Models\Plan;
use Err0r\Larasub\Models\Subscription;

trait HasSubscription
{
    public function subscriptions()
    {
        return $this->morphMany(config('larasub.models.subscription'), 'subscriber');
    }

    /**
     * Subscribe the user to a plan
     *
     * @param  Plan  $plan
     * @param  SubscriptionStatus  $status
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
}
