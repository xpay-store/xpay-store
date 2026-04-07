<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $fillable = [
        'telegram_id',
        'username',
        'email',
        'balance',
        'role',
        'is_banned',
        'supabase_uid',
    ];

    protected $casts = [
        'telegram_id' => 'integer',
        'balance' => 'array',
        'is_banned' => 'boolean',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isAgent();
    }

    public function getBalanceUsdAttribute(): float
    {
        return (float) ($this->balance['USD'] ?? 0);
    }

    public function getBalanceSypAttribute(): float
    {
        return (float) ($this->balance['SYP'] ?? 0);
    }
}
