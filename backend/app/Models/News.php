<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class News extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'news';

    protected $fillable = [
        'title',
        'type',
        'content',
        'image',
        'active',
        'published_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'published_at' => 'datetime',
    ];
}

