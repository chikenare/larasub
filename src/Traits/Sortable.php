<?php

namespace Err0r\Larasub\Traits;

trait Sortable
{
    public function scopeSorted($query, string $direction = 'asc')
    {
        return $query->orderBy('sort_order', $direction);
    }
}
