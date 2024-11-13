<?php

namespace Err0r\Larasub\Services;

use Carbon\Carbon;
use Err0r\Larasub\Facades\PeriodService;

final class PlanService
{
    /**
     * @param  \Err0r\Larasub\Models\Plan  $plan
     * @param  Carbon  $startAt
     */
    public function getPlanEndAt($plan, $startAt): ?Carbon
    {
        $endAt = null;

        if ($plan->reset_period !== null && $plan->reset_period_type !== null) {
            $endAt = $startAt->copy()->addDays(PeriodService::getDays($plan->reset_period, $plan->reset_period_type));
        }

        return $endAt;
    }
}
