<?php

namespace Err0r\Larasub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'price' => $this->price,
            'currency' => $this->currency,
            'reset_period' => $this->reset_period,
            'reset_period_type' => $this->reset_period_type,
            'sort_order' => $this->sort_order,
            'features' => PlanFeatureResource::collection($this->whenLoaded('features')),
        ];
    }
}
