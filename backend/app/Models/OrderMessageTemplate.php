<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OrderMessageTemplate extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'order_message_templates';

    protected $fillable = [
        'key',
        'title',
        'body',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}

