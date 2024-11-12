<?php

namespace Err0r\Larasub\Models;

use Err0r\Larasub\Enums\Period;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuids;
    use SoftDeletes;

    public $translatable = ['name', 'description', 'currency'];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'price',
        'currency',
        'reset_period',
        'reset_period_type',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'float',
        'reset_period' => 'integer',
        'reset_period_type' => Period::class,
        'sort_order' => 'integer',
    ];

    /**
     * @return HasMany<PlanFeature>
     */
    public function features(): HasMany
    {
        return $this->hasMany(config('larasub.models.plan_feature'));
    }

    /**
     * @return HasMany<PlanFeature>
     */
    public function feature(string $slug): HasMany
    {
        return $this->features()->whereHas('feature', fn($q) => $q->where('slug', $slug));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('larasub.models.subscription'));
    }
}
