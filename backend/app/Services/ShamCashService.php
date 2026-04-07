<?php

namespace App\Services;

use App\Models\Setting;

class ShamCashService
{
    public function walletAddress(): string
    {
        $s = Setting::general();

        return (string) ($s->sham_cash_wallet ?? '');
    }
}
