<?php

namespace App\Services;

use App\Models\Setting;

class BinancePayService
{
    /**
     * @return array{id: string, memo: string}
     */
    public function credentials(): array
    {
        $s = Setting::general();

        return [
            'id' => (string) ($s->binance_pay_id ?? ''),
            'memo' => (string) ($s->binance_memo ?? ''),
        ];
    }
}
