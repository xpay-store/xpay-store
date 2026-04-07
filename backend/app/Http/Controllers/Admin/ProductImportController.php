<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Services\ProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductImportController extends Controller
{
    public function __construct(
        private ProviderService $providerService
    ) {}

    public function import(Request $request): JsonResponse
    {
        $mode = (string) $request->input('mode', 'all');

        $imported = 0;

        if ($mode === 'mersal' || $mode === 'all') {
            $imported += $this->providerService->syncFromMersalEnv();
        }

        if ($mode === 'providers' || $mode === 'all') {
            $providers = Provider::query()->where('active', true)->get();
            foreach ($providers as $provider) {
                $imported += $this->providerService->syncProvider($provider);
            }
        }

        return response()->json([
            'message' => 'Import finished.',
            'items_touched' => $imported,
        ]);
    }
}
