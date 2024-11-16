<?php

namespace Err0r\Larasub\Traits;

trait Sluggable
{
    public function scopeSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
