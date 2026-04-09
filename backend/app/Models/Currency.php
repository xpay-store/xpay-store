<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Currency extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'currencies';

    protected $fillable = [
        'code',
        'name',
        'rate',
        'symbol',
        'active',
    ];

    protected $casts = [
        'rate' => 'float',
        'active' => 'boolean',
    ];
}

