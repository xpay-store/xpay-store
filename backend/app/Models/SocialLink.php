<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class SocialLink extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'social_links';

    protected $fillable = [
        'platform',
        'url',
        'icon',
        'order',
        'active',
    ];

    protected $casts = [
        'order' => 'integer',
        'active' => 'boolean',
    ];
}

