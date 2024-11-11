<?php

namespace Err0r\Larasub\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SubscriptionStatus extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuids;
    public $translatable = ['name'];
}
