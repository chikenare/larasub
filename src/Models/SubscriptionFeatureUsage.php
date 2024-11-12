<?php

namespace Err0r\Larasub\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionFeatureUsage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'subscription_id',
        'feature_id',
        'value',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.subscription'));
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.feature'));
    }
}
