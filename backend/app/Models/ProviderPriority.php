<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProviderPriority extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'provider_priorities';

    protected $fillable = [
        'provider_id',
        'priority',
        'active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'active' => 'boolean',
    ];
}

