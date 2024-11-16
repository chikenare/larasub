<?php

namespace Err0r\Larasub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'display_value' => $this->display_value,
            'reset_period' => $this->reset_period,
            'reset_period_type' => $this->reset_period_type,
            'sort_order' => $this->sort_order,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'feature' => new FeatureResource($this->whenLoaded('feature')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
