<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateGeneral(Request $request): JsonResponse
    {
        $data = $request->validate([
            'support_whatsapp' => ['sometimes', 'string', 'max:64'],
            'sham_cash_wallet' => ['sometimes', 'string', 'max:200'],
            'binance_pay_id' => ['sometimes', 'string', 'max:200'],
            'binance_memo' => ['sometimes', 'string', 'max:200'],
            'usd_to_syp_rate' => ['sometimes', 'numeric'],
            'min_deposit_usd' => ['sometimes', 'numeric'],
            'store_notice' => ['sometimes', 'string', 'max:2000'],
        ]);

        $doc = Setting::general();
        $doc->fill($data);
        $doc->save();

        return response()->json(['settings' => $doc]);
    }
}
