<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DepositTimeoutCommand extends Command
{
    protected $signature = 'deposit:timeout';

    protected $description = 'Reject pending deposits older than 24 hours.';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subHours(24);

        $deposits = Deposit::query()
            ->where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->get();

        $n = 0;
        foreach ($deposits as $deposit) {
            $deposit->status = 'rejected';
            $deposit->reviewed_by = 'system:timeout';
            $deposit->reviewed_at = now();
            $deposit->save();
            $n++;
        }

        $this->info('Timed out deposits: '.$n);

        return self::SUCCESS;
    }
}
