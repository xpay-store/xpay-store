<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use MongoDB\Laravel\Eloquent\Model;

class Provider extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'providers';

    protected $fillable = [
        'name',
        'type',
        'api_url',
        'api_token',
        'balance',
        'active',
        'last_sync',
    ];

    protected $casts = [
        'balance' => 'float',
        'active' => 'boolean',
        'last_sync' => 'datetime',
    ];

    protected $hidden = [
        'api_token',
    ];

    public function setApiTokenAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['api_token'] = null;

            return;
        }
        $this->attributes['api_token'] = Crypt::encryptString($value);
    }

    public function decryptApiToken(): ?string
    {
        $raw = $this->attributes['api_token'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }
        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable) {
            return null;
        }
    }
}
