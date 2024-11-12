<?php

namespace Err0r\Larasub\Models;

use Err0r\Larasub\Enums\Period;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class PlanFeature extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuids;

    public $translatable = ['display_value'];

    protected $fillable = [
        'plan_id',
        'feature_id',
        'value',
        'display_value',
        'reset_period',
        'reset_period_type',
        'sort_order',
    ];

    protected $casts = [
        'property' => 'array',
        'reset_period' => 'integer',
        'reset_period_type' => Period::class,
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.plan'));
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(config('larasub.models.feature'));
    }
}
