<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendDailyReportCommand extends Command
{
    protected $signature = 'report:daily';

    protected $description = 'Send daily sales summary to admin email.';

    public function handle(): int
    {
        $to = config('app.admin_email');
        if (! is_string($to) || $to === '') {
            $this->warn('ADMIN_EMAIL not configured.');

            return self::SUCCESS;
        }

        $start = Carbon::now()->subDay()->startOfDay();
        $end = Carbon::now()->subDay()->endOfDay();

        $orders = Order::query()
            ->where('status', 'accept')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $usd = 0.0;
        $syp = 0.0;
        foreach ($orders as $o) {
            $tp = $o->total_price ?? ['USD' => 0, 'SYP' => 0];
            $usd += (float) ($tp['USD'] ?? 0);
            $syp += (float) ($tp['SYP'] ?? 0);
        }

        $body = "XPayStore Daily Sales Report\n";
        $body .= 'Date: '.$start->toDateString()."\n";
        $body .= 'Orders: '.$orders->count()."\n";
        $body .= 'Total USD: '.$usd."\n";
        $body .= 'Total SYP: '.$syp."\n";

        try {
            Mail::raw($body, function ($message) use ($to) {
                $message->to($to)->subject('XPayStore Daily Sales Report');
            });
        } catch (\Throwable $e) {
            $this->error('Mail failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Report sent.');

        return self::SUCCESS;
    }
}
