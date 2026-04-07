<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ProviderService
{
    private Client $http;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client(['timeout' => 60]);
    }

    public function syncProvider(Provider $provider): int
    {
        if (! $provider->active) {
            return 0;
        }

        $token = $provider->decryptApiToken();
        $base = rtrim((string) $provider->api_url, '/');
        if ($base === '') {
            return 0;
        }

        $headers = [
            'Accept' => 'application/json',
        ];
        if (is_string($token) && $token !== '') {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        try {
            $catalogUrl = $base.'/catalog';
            $res = $this->http->get($catalogUrl, [
                'headers' => $headers,
                'http_errors' => false,
                'timeout' => 45,
            ]);
            $status = $res->getStatusCode();
            $body = (string) $res->getBody();
            if ($status < 200 || $status >= 300) {
                Log::warning('provider.sync: non-OK status', ['status' => $status, 'body' => $body]);

                return 0;
            }

            $json = json_decode($body, true);
            if (! is_array($json)) {
                return 0;
            }

            $count = 0;

            $categories = Arr::get($json, 'categories', []);
            if (is_array($categories)) {
                foreach ($categories as $c) {
                    if (! is_array($c)) {
                        continue;
                    }
                    $externalId = (string) ($c['id'] ?? $c['_id'] ?? '');
                    if ($externalId === '') {
                        continue;
                    }
                    Category::query()->updateOrCreate(
                        ['provider_category_id' => $externalId],
                        [
                            'name' => (string) ($c['name'] ?? 'Category'),
                            'parent_id' => (string) ($c['parent_id'] ?? '0'),
                            'image' => (string) ($c['image'] ?? ''),
                            'order' => (int) ($c['order'] ?? 0),
                            'active' => (bool) ($c['active'] ?? true),
                        ]
                    );
                }
            }

            $products = Arr::get($json, 'products', []);
            if (! is_array($products)) {
                $products = [];
            }

            foreach ($products as $p) {
                if (! is_array($p)) {
                    continue;
                }
                $pid = (string) ($p['id'] ?? $p['product_id'] ?? '');
                if ($pid === '') {
                    continue;
                }

                $catExternal = (string) ($p['category_id'] ?? '');
                $category = $catExternal !== ''
                    ? Category::query()->where('provider_category_id', $catExternal)->first()
                    : null;

                $usd = (float) ($p['price_usd'] ?? $p['price']['USD'] ?? 0);
                $syp = (float) ($p['price_syp'] ?? $p['price']['SYP'] ?? 0);
                $baseUsd = (float) ($p['base_price_usd'] ?? $usd);
                $baseSyp = (float) ($p['base_price_syp'] ?? $syp);
                $profit = (float) ($p['profit_percent'] ?? 0);

                Product::query()->updateOrCreate(
                    [
                        'provider_id' => (string) $provider->_id,
                        'provider_product_id' => $pid,
                    ],
                    [
                        'name' => (string) ($p['name'] ?? 'Product'),
                        'category_id' => $category ? (string) $category->_id : null,
                        'price' => ['USD' => $usd, 'SYP' => $syp],
                        'base_price' => ['USD' => $baseUsd, 'SYP' => $baseSyp],
                        'profit_percent' => $profit,
                        'params' => is_array($p['params'] ?? null) ? $p['params'] : [],
                        'qty_values' => is_array($p['qty_values'] ?? null) ? $p['qty_values'] : [],
                        'available' => (bool) ($p['available'] ?? true),
                        'image' => (string) ($p['image'] ?? ''),
                        'product_type' => in_array(($p['product_type'] ?? 'amount'), ['package', 'amount'], true)
                            ? $p['product_type']
                            : 'amount',
                    ]
                );
                $count++;
            }

            $provider->last_sync = now();
            $provider->save();

            return $count;
        } catch (\Throwable $e) {
            Log::error('provider.sync: '.$e->getMessage());

            return 0;
        }
    }

    /**
     * Attempt to fulfill an order via provider HTTP API.
     *
     * @return array{ok: bool, response: array<string, mixed>|null, error?: string}
     */
    public function fulfillOrder(Order $order, Product $product, Provider $provider): array
    {
        $token = $provider->decryptApiToken();
        $base = rtrim((string) $provider->api_url, '/');
        if ($base === '') {
            return ['ok' => false, 'response' => null, 'error' => 'missing_provider_url'];
        }

        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        if (is_string($token) && $token !== '') {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        $payload = [
            'product_id' => $product->provider_product_id,
            'quantity' => $order->quantity,
            'params' => $order->params ?? [],
            'client_order_id' => (string) $order->order_uuid,
        ];

        try {
            $res = $this->http->post($base.'/orders', [
                'headers' => $headers,
                'json' => $payload,
                'timeout' => 60,
                'http_errors' => false,
            ]);

            $status = $res->getStatusCode();
            $body = (string) $res->getBody();
            $json = json_decode($body, true);
            $response = is_array($json) ? $json : ['raw' => $body];

            if ($status >= 200 && $status < 300) {
                return ['ok' => true, 'response' => $response];
            }

            return ['ok' => false, 'response' => $response, 'error' => 'http_'.$status];
        } catch (\Throwable $e) {
            Log::error('provider.fulfill: '.$e->getMessage());

            return ['ok' => false, 'response' => null, 'error' => $e->getMessage()];
        }
    }

    public function fetchProviderBalance(Provider $provider): ?float
    {
        $token = $provider->decryptApiToken();
        $base = rtrim((string) $provider->api_url, '/');
        if ($base === '') {
            return null;
        }

        $headers = ['Accept' => 'application/json'];
        if (is_string($token) && $token !== '') {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        try {
            $res = $this->http->get($base.'/balance', [
                'headers' => $headers,
                'timeout' => 20,
                'http_errors' => false,
            ]);
            if ($res->getStatusCode() < 200 || $res->getStatusCode() >= 300) {
                return null;
            }
            $json = json_decode((string) $res->getBody(), true);
            if (! is_array($json)) {
                return null;
            }
            $bal = $json['balance'] ?? $json['data']['balance'] ?? null;

            return is_numeric($bal) ? (float) $bal : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function syncFromMersalEnv(): int
    {
        $url = rtrim((string) config('services.mersal.url'), '/');
        $token = (string) config('services.mersal.token');
        if ($url === '') {
            return 0;
        }

        $provider = Provider::query()->firstOrCreate(
            ['name' => 'Mersal (env)'],
            [
                'type' => 'custom',
                'api_url' => $url,
                'api_token' => $token !== '' ? $token : null,
                'balance' => 0,
                'active' => true,
                'last_sync' => null,
            ]
        );

        if ($token !== '') {
            $provider->api_token = $token;
        }
        $provider->api_url = $url;
        $provider->active = true;
        $provider->save();

        return $this->syncProvider($provider);
    }
}
