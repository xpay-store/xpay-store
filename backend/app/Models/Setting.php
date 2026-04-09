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
        'site_name',
        'logo_url',
        'support_whatsapp',
        'sham_cash_wallet',
        'binance_pay_id',
        'binance_memo',
        'usd_to_syp_rate',
        'min_deposit_usd',
        'store_notice',
        'primary_color',
        'secondary_color',
        'enable_dark_mode',
        'card_style',
    ];

    protected $casts = [
        'usd_to_syp_rate' => 'float',
        'min_deposit_usd' => 'float',
        'enable_dark_mode' => 'boolean',
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
            'site_name' => env('APP_NAME', 'XPayStore'),
            'logo_url' => '',
            'sham_cash_wallet' => env('SHAM_CASH_WALLET', ''),
            'binance_pay_id' => env('BINANCE_PAY_ID', ''),
            'binance_memo' => env('BINANCE_PAY_MEMO', ''),
            'usd_to_syp_rate' => (float) env('DEFAULT_USD_SYP_RATE', 15000),
            'min_deposit_usd' => (float) env('DEFAULT_MIN_DEPOSIT_USD', 1),
            'store_notice' => '',
            'primary_color' => '#16a34a',
            'secondary_color' => '#0f172a',
            'enable_dark_mode' => true,
            'card_style' => 'rounded',
        ]);
    }
}
