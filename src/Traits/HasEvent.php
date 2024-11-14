<?php

namespace Err0r\Larasub\Traits;

use Err0r\Larasub\Models\Event;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasEvent
{
    public function events(): MorphMany
    {
        return $this->morphMany(config('larasub.models.event'), 'eventable');
    }

    public function scopeWhereEventType($query, string $eventType)
    {
        return $query->whereHas('events', function ($query) use ($eventType) {
            $query->where('event_type', $eventType);
        });
    }

    /**
     * Add an event to the model.
     *
     * @return Event
     */
    public function addEvent(string $eventType)
    {
        return $this->events()->create([
            'event_type' => $eventType,
        ]);
    }
}
