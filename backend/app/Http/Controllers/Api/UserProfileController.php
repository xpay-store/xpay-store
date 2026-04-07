<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $general = Setting::general();

        return response()->json([
            'user' => $user,
            'settings' => [
                'support_whatsapp' => $general->support_whatsapp,
                'store_notice' => $general->store_notice,
                'sham_cash_wallet' => $general->sham_cash_wallet,
                'binance_pay_id' => $general->binance_pay_id,
                'binance_memo' => $general->binance_memo,
                'min_deposit_usd' => (float) ($general->min_deposit_usd ?? 0),
                'usd_to_syp_rate' => (float) ($general->usd_to_syp_rate ?? 0),
            ],
        ]);
    }
}
