<?php

namespace Err0r\Larasub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'cancelled_at' => $this->cancelled_at,
            'subscriber' => $this->whenLoaded('subscriber'),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'features_usage' => SubscriptionFeatureUsageResource::collection($this->whenLoaded('featuresUsage')),
        ];
    }
}
