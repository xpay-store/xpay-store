<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class ProfitChart extends ChartWidget
{
    protected static ?string $heading = 'الأرباح (آخر 30 يوماً)';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('m-d');
            $orders = Order::query()
                ->where('status', 'accept')
                ->whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
                ->get();

            $usd = 0.0;
            foreach ($orders as $order) {
                $usd += (float) ($order->total_price['USD'] ?? 0);
            }
            $values[] = round($usd, 2);
        }

        return [
            'datasets' => [[
                'label' => 'Profit USD',
                'data' => $values,
                'borderColor' => '#22C55E',
                'backgroundColor' => 'rgba(34, 197, 94, 0.12)',
                'tension' => 0.35,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

