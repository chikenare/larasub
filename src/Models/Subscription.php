<?php

namespace Err0r\Larasub\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'plan_id',
        'status_id',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.plan'));
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.subscription_status'));
    }

    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            config('larasub.models.feature'),
            config('larasub.tables.subscription_feature_usage.name')
        )->withPivot([
            'value',
        ]);
    }
}
