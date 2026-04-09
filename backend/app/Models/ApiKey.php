<?php

namespace App\Models;

use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Model;

class ApiKey extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'api_keys';

    protected $fillable = [
        'name',
        'key',
        'permissions',
        'active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (! $model->key) {
                $model->key = 'xpk_'.Str::random(48);
            }
        });
    }
}

