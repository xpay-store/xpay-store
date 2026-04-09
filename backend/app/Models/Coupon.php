<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Coupon extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'coupons';

    protected $fillable = [
        'code',
        'discount_percent',
        'max_uses',
        'used_count',
        'active',
        'expires_at',
    ];

    protected $casts = [
        'discount_percent' => 'float',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];
}

