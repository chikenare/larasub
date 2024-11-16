<?php

namespace Err0r\Larasub\Models;

use Carbon\Carbon;
use Err0r\Larasub\Enums\FeatureType;
use Err0r\Larasub\Facades\PlanService;
use Err0r\Larasub\Facades\SubscriptionService;
use Err0r\Larasub\Traits\HasEvent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasEvent;
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'plan_id',
        'start_at',
        'end_at',
        'cancelled_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('larasub.tables.subscriptions.name'));
    }

    /**
     * @return BelongsTo<Plan>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.plan'));
    }

    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<SubscriptionFeatureUsage>
     */
    public function featuresUsage(): HasMany
    {
        return $this->hasMany(config('larasub.models.subscription_feature_usages'));
    }

    /**
     * @return HasMany<SubscriptionFeatureUsage>
     */
    public function featureUsage(string $slug): HasMany
    {
        return $this->featuresUsage()->whereHas('feature', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeActive($query)
    {
        return $query->where('start_at', '<=', now())
            ->where(
                fn ($q) => $q->whereNull('end_at')
                    ->orWhere('end_at', '>=', now())
            );
    }

    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_at', '<', now());
    }

    public function scopeFuture($query)
    {
        return $query->where('start_at', '>', now());
    }

    public function isActive(): bool
    {
        return ! $this->isCancelled() && ! $this->isExpired() && ! $this->isFuture();
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->end_at !== null && $this->end_at < now();
    }

    public function isFuture(): bool
    {
        return $this->start_at > now();
    }

    public function cancel(?bool $immediately = false): void
    {
        $this->cancelled_at = now();

        if ($immediately) {
            $this->end_at = now();
        }

        $this->save();
    }

    public function resume(?Carbon $startAt = null, ?Carbon $endAt = null): void
    {
        $this->cancelled_at = null;
        $this->start_at ??= $startAt;
        $this->end_at = $endAt ?? PlanService::getPlanEndAt($this->plan, $this->start_at);
        $this->save();
    }

    public function feature(string $slug)
    {
        return $this->plan->feature($slug)->first();
    }

    public function hasFeature(string $slug): bool
    {
        return $this->plan->feature($slug)->exists();
    }

    public function remainingFeatureUsage(string $slug): ?float
    {
        /** @var PlanFeature|null */
        $planFeature = $this->plan->feature($slug)->first();

        if ($planFeature === null) {
            throw new \InvalidArgumentException("The feature '$slug' is not part of the plan");
        }

        if ($planFeature->feature->type == FeatureType::NON_CONSUMABLE || $planFeature->value === null) {
            throw new \InvalidArgumentException("The feature '$slug' is not consumable or has no value");
        }

        if ($planFeature->isUnlimited()) {
            return floatval(INF);
        }

        $featureUsage = SubscriptionService::totalFeatureUsageInPeriod($this, $slug);

        return $planFeature->value - $featureUsage;
    }

    /**
     * check if the subscription can use a feature
     *
     * @param  mixed  $value
     */
    public function canUseFeature(string $slug, float $value): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        if ($value <= 0) {
            throw new \InvalidArgumentException('Usage value must be greater than 0');
        }

        /** @var PlanFeature|null */
        $planFeature = $this->plan->feature($slug)->first();

        if ($planFeature === null) {
            throw new \InvalidArgumentException("The feature '$slug' is not part of the plan");
        }

        if ($planFeature->feature->type == FeatureType::NON_CONSUMABLE) {
            return false;
        }

        return $this->remainingFeatureUsage($slug) >= $value;
    }

    /**
     * create a new subscription feature usage record (if applicable)
     *
     * @param  mixed  $value
     * @return SubscriptionFeatureUsage
     *
     * @throws \InvalidArgumentException
     */
    public function useFeature(string $slug, float $value)
    {
        if (! $this->canUseFeature($slug, $value)) {
            throw new \InvalidArgumentException("The feature '$slug' cannot be used");
        }

        /** @var PlanFeature */
        $planFeature = $this->plan->feature($slug)->first();

        /** @var SubscriptionFeatureUsage */
        $featureUsage = $this->featuresUsage()->create([
            'feature_id' => $planFeature->feature->id,
            'value' => $value,
        ]);

        return $featureUsage;
    }
}
