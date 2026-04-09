<?php

namespace App\Filament\Widgets;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('المستخدمون', (string) User::query()->count()),
            Stat::make('الطلبات', (string) Order::query()->count()),
            Stat::make('المنتجات', (string) Product::query()->count()),
            Stat::make('إيداعات معلقة', (string) Deposit::query()->where('status', 'pending')->count()),
        ];
    }
}

