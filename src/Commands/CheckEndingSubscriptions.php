<?php

namespace Err0r\Larasub\Commands;

use Err0r\Larasub\Events\SubscriptionEnded;
use Err0r\Larasub\Events\SubscriptionEndingSoon;
use Err0r\Larasub\Models\Subscription;
use Illuminate\Console\Command;

class CheckEndingSubscriptions extends Command
{
    protected $signature = 'larasub:check-ending-subscriptions';

    protected $description = 'Check and fire events for subscriptions that are ending';

    public function handle()
    {
        $this->processEndedSubscriptions();
        $this->processEndingSoonSubscriptions();
    }

    private function processEndedSubscriptions(): void
    {
        /** @var Subscription */
        $subscriptionModel = config('larasub.models.subscription');

        $endedSubscriptions = $subscriptionModel::query()
            ->where('end_at', '<=', now())
            ->where('end_at', '>', now()->subMinutes(5))
            ->whereDoesntHave('events', function ($query) {
                $query->whereEventType(SubscriptionEnded::class)->where('created_at', '>', now()->subMinutes(5));
            })
            ->get();

        foreach ($endedSubscriptions as $subscription) {
            event(new SubscriptionEnded($subscription));
            $subscription->addEvent(SubscriptionEnded::class);
        }
    }

    private function processEndingSoonSubscriptions(): void
    {
        /** @var \Err0r\Larasub\Models\Subscription */
        $subscriptionModel = config('larasub.models.subscription');

        $endingSoonDays = config('larasub.scheduling.ending_soon_days');

        $endingSoonSubscriptions = $subscriptionModel::query()
            ->where('end_at', '>', now())
            ->where('end_at', '<=', now()->addDays($endingSoonDays))
            ->whereDoesntHave('events', function ($query) use ($endingSoonDays) {
                $query->whereEventType(SubscriptionEndingSoon::class)->where('created_at', '>', now()->subDays($endingSoonDays));
            })
            ->get();

        foreach ($endingSoonSubscriptions as $subscription) {
            event(new SubscriptionEndingSoon($subscription));
            $subscription->addEvent(SubscriptionEndingSoon::class);
        }
    }
}
