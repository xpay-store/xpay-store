<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Deposit extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'deposits';

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'method',
        'transaction_id',
        'proof_image',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
