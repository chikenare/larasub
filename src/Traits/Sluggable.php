<?php

namespace Err0r\Larasub\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sluggable
{
    public function scopeSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
