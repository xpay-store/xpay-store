<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function sales(Request $request): JsonResponse
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $fromC = is_string($from) && $from !== '' ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay();
        $toC = is_string($to) && $to !== '' ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        $orders = Order::query()
            ->where('status', 'accept')
            ->whereBetween('created_at', [$fromC, $toC])
            ->get();

        $usd = 0.0;
        $syp = 0.0;
        foreach ($orders as $o) {
            $tp = $o->total_price ?? ['USD' => 0, 'SYP' => 0];
            $usd += (float) ($tp['USD'] ?? 0);
            $syp += (float) ($tp['SYP'] ?? 0);
        }

        return response()->json([
            'from' => $fromC->toIso8601String(),
            'to' => $toC->toIso8601String(),
            'orders_count' => $orders->count(),
            'totals' => ['USD' => $usd, 'SYP' => $syp],
        ]);
    }
}
