<?php

namespace Err0r\Larasub\Events;

use Err0r\Larasub\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionEnded
{
    use Dispatchable, SerializesModels;

    public function __construct(public Subscription $subscription) {}
}
