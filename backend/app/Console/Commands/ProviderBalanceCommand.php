<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Services\ProviderService;
use Illuminate\Console\Command;

class ProviderBalanceCommand extends Command
{
    protected $signature = 'provider:balance';

    protected $description = 'Fetch provider balances from remote APIs.';

    public function handle(ProviderService $service): int
    {
        $providers = Provider::query()->where('active', true)->get();
        foreach ($providers as $provider) {
            $balance = $service->fetchProviderBalance($provider);
            if ($balance !== null) {
                $provider->balance = $balance;
                $provider->save();
                $this->line($provider->name.': '.$balance);
            }
        }

        return self::SUCCESS;
    }
}
