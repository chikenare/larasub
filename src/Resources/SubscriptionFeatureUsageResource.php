<?php

namespace Err0r\Larasub\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionFeatureUsageResource extends JsonResource
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
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'feature' => new FeatureResource($this->whenLoaded('feature')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
