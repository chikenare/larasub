<?php

namespace Err0r\Larasub\Models;

use Err0r\Larasub\Enums\FeatureType;
use Err0r\Larasub\Enums\Period;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Feature extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'consumable',
        'reset_period',
        'reset_period_type',
        'sort_order',
    ];

    protected $casts = [
        'type' => FeatureType::class,
        'reset_period' => 'integer',
        'reset_period_type' => Period::class,
        'sort_order' => 'integer',
    ];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(
            config('larasub.models.plan'),
            config('larasub.tables.plan_features.name')
        )->withPivot([
            'value',
            'sort_order',
        ]);
    }
}
