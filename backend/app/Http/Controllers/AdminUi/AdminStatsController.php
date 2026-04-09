<?php

namespace App\Http\Controllers\AdminUi;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminStatsController extends Controller
{
    public function overview(): JsonResponse
    {
        $usersCount = User::query()->count();
        $ordersCount = Order::query()->count();
        $productsCount = Product::query()->count();
        $pendingDeposits = Deposit::query()->where('status', 'pending')->count();

        $totals = ['USD' => 0.0, 'SYP' => 0.0];

        try {
            $cursor = Deposit::raw(function ($collection) {
                return $collection->aggregate([
                    ['$match' => ['status' => 'approved']],
                    [
                        '$group' => [
                            '_id' => null,
                            'usd' => ['$sum' => ['$ifNull' => ['$amount.USD', 0]]],
                            'syp' => ['$sum' => ['$ifNull' => ['$amount.SYP', 0]]],
                        ],
                    ],
                ]);
            });

            foreach ($cursor as $row) {
                $totals['USD'] = (float) ($row['usd'] ?? 0);
                $totals['SYP'] = (float) ($row['syp'] ?? 0);
                break;
            }
        } catch (\Throwable) {
            // keep defaults
        }

        return response()->json([
            'users' => (int) $usersCount,
            'orders' => (int) $ordersCount,
            'products' => (int) $productsCount,
            'pending_deposits' => (int) $pendingDeposits,
            'approved_deposits_total' => $totals,
        ]);
    }
}

