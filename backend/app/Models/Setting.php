<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Setting extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'settings';

    protected $primaryKey = '_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        '_id',
        'support_whatsapp',
        'sham_cash_wallet',
        'binance_pay_id',
        'binance_memo',
        'usd_to_syp_rate',
        'min_deposit_usd',
        'store_notice',
    ];

    protected $casts = [
        'usd_to_syp_rate' => 'float',
        'min_deposit_usd' => 'float',
    ];

    public static function general(): self
    {
        $doc = static::query()->where('_id', 'general')->first();
        if ($doc) {
            return $doc;
        }

        return static::query()->create([
            '_id' => 'general',
            'support_whatsapp' => env('DEFAULT_SUPPORT_WHATSAPP', ''),
            'sham_cash_wallet' => env('SHAM_CASH_WALLET', ''),
            'binance_pay_id' => env('BINANCE_PAY_ID', ''),
            'binance_memo' => env('BINANCE_PAY_MEMO', ''),
            'usd_to_syp_rate' => (float) env('DEFAULT_USD_SYP_RATE', 15000),
            'min_deposit_usd' => (float) env('DEFAULT_MIN_DEPOSIT_USD', 1),
            'store_notice' => '',
        ]);
    }
}
