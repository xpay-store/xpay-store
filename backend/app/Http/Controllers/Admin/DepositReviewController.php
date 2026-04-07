<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\DepositReviewService;
use Illuminate\Http\JsonResponse;

class DepositReviewController extends Controller
{
    public function __construct(
        private DepositReviewService $reviews
    ) {}

    public function pending(): JsonResponse
    {
        $items = Deposit::query()
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->limit(500)
            ->get();

        return response()->json(['data' => $items]);
    }

    public function approve(string $id): JsonResponse
    {
        $deposit = Deposit::query()->where('_id', $id)->first();
        if ($deposit === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $ok = $this->reviews->approve($deposit, 'admin_api');
        if (! $ok) {
            return response()->json(['message' => 'Unable to approve.'], 409);
        }

        return response()->json(['deposit' => $deposit->fresh()]);
    }

    public function reject(string $id): JsonResponse
    {
        $deposit = Deposit::query()->where('_id', $id)->first();
        if ($deposit === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $ok = $this->reviews->reject($deposit, 'admin_api');
        if (! $ok) {
            return response()->json(['message' => 'Unable to reject.'], 409);
        }

        return response()->json(['deposit' => $deposit->fresh()]);
    }
}
