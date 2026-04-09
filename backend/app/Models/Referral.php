<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Referral extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'referrals';

    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'reward_referrer',
        'reward_referred',
        'status',
    ];

    protected $casts = [
        'reward_referrer' => 'float',
        'reward_referred' => 'float',
    ];
}

