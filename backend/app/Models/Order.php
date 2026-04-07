<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'orders';

    protected $fillable = [
        'order_uuid',
        'order_number',
        'user_id',
        'product_id',
        'quantity',
        'params',
        'total_price',
        'status',
        'provider_response',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'params' => 'array',
        'total_price' => 'array',
        'provider_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', '_id');
    }
}
