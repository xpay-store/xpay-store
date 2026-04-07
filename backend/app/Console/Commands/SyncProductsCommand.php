<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Services\ProviderService;
use Illuminate\Console\Command;

class SyncProductsCommand extends Command
{
    protected $signature = 'sync:products';

    protected $description = 'Synchronize products and categories from configured providers (including Mersal env).';

    public function handle(ProviderService $service): int
    {
        $total = $service->syncFromMersalEnv();

        $providers = Provider::query()->where('active', true)->get();
        foreach ($providers as $provider) {
            $total += $service->syncProvider($provider);
        }

        $this->info('Sync completed. Items touched (approx): '.$total);

        return self::SUCCESS;
    }
}
