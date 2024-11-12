<?php

namespace Err0r\Larasub\Models;

use Err0r\Larasub\Enums\FeatureType;
use Facades\Err0r\Larasub\Services\PeriodService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
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
        return $this->hasMany(config('larasub.models.subscription_feature_usage'));
    }

    /**
     * @param  string  $slug
     * @return HasMany<SubscriptionFeatureUsage>
     */
    public function featureUsage(string $slug): HasMany
    {
        return $this->featuresUsage()->whereHas('feature', fn($q) => $q->where('slug', $slug));
    }

    public function scopeActive($query)
    {
        return $query->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
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
        return $this->start_at <= now() && $this->end_at >= now();
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->end_at < now();
    }

    public function isFuture(): bool
    {
        return $this->start_at > now();
    }

    public function cancel(): void
    {
        $this->cancelled_at = now();
        $this->save();
    }

    public function resume(): void
    {
        $this->cancelled_at = null;
        $this->save();
    }

    /**
     * check if the subscription can use a feature
     * @param string $slug
     * @param mixed $value
     * @return bool
     */
    public function canUseFeature(string $slug, $value = null): bool
    {
        /** @var PlanFeature|null */
        $planFeature = $this->plan->feature($slug)->first();

        if ($planFeature === null) {
            return false;
        }

        if ($planFeature->type == FeatureType::NON_CONSUMABLE) {
            return true;
        }

        // plan's feature is consumable but unlimited
        if ($planFeature->value === null) {
            return true;
        }

        $usages = $this->featureUsage($slug);
        if ($planFeature->reset_period !== null && $planFeature->reset_period_type !== null) {
            $resetPeriod = $planFeature->reset_period;
            $resetPeriodType = $planFeature->reset_period_type;
            $resetMinutes = PeriodService::getMinutes($resetPeriod, $resetPeriodType);
            $usages = $usages->where('created_at', '>=', now()->subMinutes($resetMinutes));
        }

        $featureUsage = $usages->sum('value');

        return $planFeature->value > ($featureUsage + $value);
    }

    /**
     * create a new subscription feature usage record (if applicable)
     * @param string $slug
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return SubscriptionFeatureUsage
     */
    public function useFeature(string $slug, $value = null)
    {
        /** @var PlanFeature|null */
        $planFeature = $this->plan->feature($slug)->first();

        if ($planFeature === null) {
            throw new \InvalidArgumentException("The feature with slug $slug does not exist on the plan");
        }

        if (! $this->canUseFeature($slug, $value)) {
            throw new \InvalidArgumentException("The feature with slug $slug cannot be used");
        }

        $featureUsage = $this->featureUsage($slug)->first();

        $newUsage = $featureUsage?->value ?? 0;
        if ($value !== null) {
            $newUsage += $value;
        }

        /** @var SubscriptionFeatureUsage */
        $featureUsage = $this->featuresUsage()->create([
            'feature_id' => $planFeature->id,
            'value' => $newUsage,
        ]);

        return $featureUsage;
    }
}
