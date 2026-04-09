<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrdersTable extends BaseWidget
{
    protected static ?string $heading = 'آخر 5 طلبات';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Order::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('رقم الطلب'),
                Tables\Columns\TextColumn::make('status')->badge()->label('الحالة'),
                Tables\Columns\TextColumn::make('total_price.USD')->label('USD'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('الوقت'),
            ])
            ->paginated(false);
    }
}

