<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AutoCode extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'auto_codes';

    protected $fillable = [
        'product_id',
        'code',
        'is_used',
        'used_by_order_id',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];
}

