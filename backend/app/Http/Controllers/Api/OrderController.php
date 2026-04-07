<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    public function __construct(
        private OrderProcessingService $orders
    ) {}

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_uuid' => ['required', 'string', 'max:128'],
            'product_id' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'params' => ['sometimes', 'array'],
        ]);

        $user = $request->user();
        $uuid = (string) $validated['order_uuid'];

        $result = $this->orders->createFromBalance(
            $user,
            $uuid,
            (string) $validated['product_id'],
            (int) $validated['quantity'],
            (array) ($validated['params'] ?? [])
        );

        if (($result['ok'] ?? false) !== true) {
            return response()->json([
                'message' => $result['error'] ?? 'order_failed',
            ], (int) ($result['http'] ?? 500));
        }

        $code = ! empty($result['idempotent']) ? 200 : 201;

        return response()->json([
            'order' => $result['order'],
            'idempotent' => (bool) ($result['idempotent'] ?? false),
            'provider_failed' => (bool) ($result['provider_failed'] ?? false),
        ], $code);
    }

    public function status(Request $request, string $order_id): JsonResponse
    {
        $user = $request->user();
        $order = Order::query()->where('_id', $order_id)->first();
        if ($order === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        if ((string) $order->user_id !== (string) $user->_id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['order' => $order]);
    }

    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();
        $filter = (string) $request->query('status', 'all');

        $q = Order::query()->where('user_id', (string) $user->_id);

        if ($filter === 'wait') {
            $q->where('status', 'wait');
        } elseif ($filter === 'accept') {
            $q->where('status', 'accept');
        } elseif ($filter === 'reject') {
            $q->where('status', 'reject');
        }

        $orders = $q->orderByDesc('created_at')->limit(200)->get();

        return response()->json(['data' => $orders]);
    }
}
