<?php

namespace Err0r\Larasub\Builders;

use Err0r\Larasub\Enums\FeatureType;
use Err0r\Larasub\Enums\FeatureValue;
use Err0r\Larasub\Enums\Period;
use Err0r\Larasub\Models\Feature;
use Err0r\Larasub\Models\Plan;

class PlanBuilder
{
    private array $attributes = [];

    private array $features = [];

    public function __construct(private string $slug)
    {
        $this->attributes['slug'] = $slug;
        $this->attributes['is_active'] = true;
        $this->attributes['price'] = 0.0;
    }

    public static function create(string $slug): self
    {
        return new self($slug);
    }

    public function name($name): self
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    public function description($description): self
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    public function price(float $price, $currency): self
    {
        $this->attributes['price'] = $price;
        $this->attributes['currency'] = $currency;

        return $this;
    }

    public function resetPeriod(int $period, Period $periodType): self
    {
        $this->attributes['reset_period'] = $period;
        $this->attributes['reset_period_type'] = $periodType;

        return $this;
    }

    public function inactive(): self
    {
        $this->attributes['is_active'] = false;

        return $this;
    }

    public function sortOrder(int $order): self
    {
        $this->attributes['sort_order'] = $order;

        return $this;
    }

    /**
     * @param  Feature|string  $feature  The feature model or slug
     * @param  callable(PlanFeatureBuilder): PlanFeatureBuilder  $callback  The callback to build the feature
     */
    public function addFeature($feature, callable $callback): self
    {
        $featureSlug = $feature instanceof Feature ? $feature->slug : $feature;

        $featureBuilder = new PlanFeatureBuilder($featureSlug);
        $callback($featureBuilder);
        $this->features[] = $featureBuilder->build();

        return $this;
    }

    public function build(): Plan
    {
        $plan = Plan::updateOrCreate(
            ['slug' => $this->attributes['slug']],
            $this->attributes
        );

        // Attach features
        foreach ($this->features as $feature) {
            $plan->features()->updateOrCreate(
                ['feature_id' => $feature['feature_id']],
                $feature
            );
        }

        return $plan;
    }
}

class PlanFeatureBuilder
{
    private array $attributes = [];

    public function __construct(private string $featureSlug)
    {
        $this->attributes['slug'] = $featureSlug;
    }

    /**
     * @param  FeatureValue|string|null  $value
     */
    public function value($value): self
    {
        $value = $value instanceof FeatureValue ? $value->value : $value;
        $this->attributes['value'] = $value;

        return $this;
    }

    /**
     * @param  string|array|null  $displayValue
     */
    public function displayValue($displayValue): self
    {
        $this->attributes['display_value'] = $displayValue;

        return $this;
    }

    public function resetPeriod(?int $resetPeriod, ?Period $resetPeriodType): self
    {
        $this->attributes['reset_period'] = $resetPeriod;
        $this->attributes['reset_period_type'] = $resetPeriodType;

        return $this;
    }

    public function sortOrder(?int $sortOrder): self
    {
        $this->attributes['sort_order'] = $sortOrder;

        return $this;
    }

    public function build(): array
    {
        $featureModel = Feature::where('slug', $this->attributes['slug'])->firstOrFail();

        if ($featureModel->type === FeatureType::CONSUMABLE && ($this->attributes['value'] ?? null) === null) {
            throw new \InvalidArgumentException("The feature '{$this->attributes['slug']}' is consumable and requires a value");
        }

        $attributes = [
            'feature_id' => $featureModel->id,
            'value' => $this->attributes['value'] ?? null,
            'display_value' => $this->attributes['display_value'] ?? null,
            'reset_period' => $this->attributes['reset_period'] ?? null,
            'reset_period_type' => $this->attributes['reset_period_type'] ?? null,
            'sort_order' => $this->attributes['sort_order'] ?? null,
        ];

        return array_filter(
            $attributes, 
            fn ($value) => $value !== null
        );
    }
}
