<?php

namespace Err0r\Larasub\Models;

use Err0r\Larasub\Builders\FeatureBuilder;
use Err0r\Larasub\Enums\FeatureType;
use Err0r\Larasub\Traits\Sluggable;
use Err0r\Larasub\Traits\Sortable;
use Err0r\Larasub\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuids;
    use Sluggable;
    use SoftDeletes;
    use Sortable;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'type' => FeatureType::class,
        'sort_order' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('larasub.tables.features.name'));
    }

    public function plans(): HasMany
    {
        return $this->hasMany(config('larasub.models.plan_feature'));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('larasub.models.subscription_feature_usages'));
    }

    public function isConsumable(): bool
    {
        return $this->type == FeatureType::CONSUMABLE;
    }

    public function isNonConsumable(): bool
    {
        return $this->type == FeatureType::NON_CONSUMABLE;
    }

    public static function builder(string $slug): FeatureBuilder
    {
        return FeatureBuilder::create($slug);
    }
}
