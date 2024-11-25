<?php

namespace Err0r\Larasub\Traits;

use Carbon\Carbon;
use Err0r\Larasub\Facades\PlanService;
use Err0r\Larasub\Models\Plan;
use Err0r\Larasub\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Subscribable
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
}
