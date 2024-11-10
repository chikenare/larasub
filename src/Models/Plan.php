<?php

namespace Err0r\Larasub\Models;

use Err0r\Larasub\Enums\Period;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            config('larasub.models.feature'),
            config('larasub.tables.plan_features.name')
        )->withPivot([
            'value',
            'sort_order',
        ]);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('larasub.models.subscription'));
    }
}
