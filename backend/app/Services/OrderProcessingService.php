<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderProcessingService
{
    public function __construct(
        private ProviderService $providers
    ) {}

    /**
     * @param  array<string, mixed>  $params
     */
    public function createFromBalance(
        User $user,
        string $orderUuid,
        string $productId,
        int $quantity,
        array $params
    ): array {
        if ($quantity < 1) {
            return ['ok' => false, 'error' => 'invalid_quantity', 'http' => 422];
        }

        $existing = Order::query()->where('order_uuid', $orderUuid)->first();
        if ($existing !== null) {
            return ['ok' => true, 'order' => $existing, 'idempotent' => true];
        }

        $product = Product::query()->where('_id', $productId)->first();
        if ($product === null || ! $product->available) {
            return ['ok' => false, 'error' => 'product_unavailable', 'http' => 404];
        }

        $priceUsd = (float) ($product->price['USD'] ?? 0) * $quantity;
        $priceSyp = (float) ($product->price['SYP'] ?? 0) * $quantity;

        $provider = Provider::query()->find($product->provider_id);
        if ($provider === null || ! $provider->active) {
            return ['ok' => false, 'error' => 'provider_unavailable', 'http' => 503];
        }

        $order = null;

        try {
            DB::connection('mongodb')->transaction(function () use ($user, $orderUuid, $product, $quantity, $params, $priceUsd, $priceSyp, &$order) {
                $fresh = User::query()->find($user->_id);
                if ($fresh === null) {
                    throw new \RuntimeException('user_missing');
                }

                $bal = $fresh->balance ?? ['USD' => 0, 'SYP' => 0];
                $haveUsd = (float) ($bal['USD'] ?? 0);
                $haveSyp = (float) ($bal['SYP'] ?? 0);
                if ($haveUsd + 1e-6 < $priceUsd || $haveSyp + 1e-6 < $priceSyp) {
                    throw new \RuntimeException('insufficient_balance');
                }

                $bal['USD'] = $haveUsd - $priceUsd;
                $bal['SYP'] = $haveSyp - $priceSyp;
                $fresh->balance = $bal;
                $fresh->save();

                $order = Order::query()->create([
                    'order_uuid' => $orderUuid,
                    'order_number' => $this->makeOrderNumber(),
                    'user_id' => (string) $fresh->_id,
                    'product_id' => (string) $product->_id,
                    'quantity' => $quantity,
                    'params' => $params,
                    'total_price' => ['USD' => $priceUsd, 'SYP' => $priceSyp],
                    'status' => 'wait',
                    'provider_response' => null,
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'insufficient_balance') {
                return ['ok' => false, 'error' => 'insufficient_balance', 'http' => 402];
            }

            return ['ok' => false, 'error' => 'order_failed', 'http' => 500];
        } catch (\Throwable) {
            return ['ok' => false, 'error' => 'order_failed', 'http' => 500];
        }

        if ($order === null) {
            return ['ok' => false, 'error' => 'order_failed', 'http' => 500];
        }

        $result = $this->providers->fulfillOrder($order, $product, $provider);

        if ($result['ok']) {
            $order->status = 'accept';
            $order->provider_response = $result['response'];
            $order->save();

            return ['ok' => true, 'order' => $order->fresh()];
        }

        $fresh = User::query()->find($user->_id);
        if ($fresh !== null) {
            $bal = $fresh->balance ?? ['USD' => 0, 'SYP' => 0];
            $bal['USD'] = (float) ($bal['USD'] ?? 0) + $priceUsd;
            $bal['SYP'] = (float) ($bal['SYP'] ?? 0) + $priceSyp;
            $fresh->balance = $bal;
            $fresh->save();
        }

        $order->status = 'reject';
        $order->provider_response = $result['response'] ?? ['error' => $result['error'] ?? 'unknown'];
        $order->save();

        return ['ok' => true, 'order' => $order->fresh(), 'provider_failed' => true];
    }

    private function makeOrderNumber(): string
    {
        return 'XP-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
    }
}
