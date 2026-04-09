<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'payment_methods';

    protected $fillable = [
        'name',
        'slug',
        'instructions',
        'qr_image',
        'wallet',
        'min_amount',
        'active',
    ];

    protected $casts = [
        'min_amount' => 'float',
        'active' => 'boolean',
    ];
}

