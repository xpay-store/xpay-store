<?php

namespace App\Filament\Widgets;

use App\Models\Deposit;
use App\Services\DepositReviewService;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingDepositsTable extends BaseWidget
{
    protected static ?string $heading = 'آخر 5 إيداعات معلقة';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Deposit::query()->where('status', 'pending')->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('_id')->label('ID')->limit(16),
                Tables\Columns\TextColumn::make('user_id')->label('المستخدم')->limit(20),
                Tables\Columns\TextColumn::make('amount.USD')->label('USD'),
                Tables\Columns\TextColumn::make('method')->label('الطريقة'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('الوقت'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('قبول')
                    ->color('success')
                    ->action(function (Deposit $record): void {
                        app(DepositReviewService::class)->approve($record, 'dashboard_widget');
                        Notification::make()->title('تم قبول الإيداع').success()->send();
                    }),
                Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->action(function (Deposit $record): void {
                        app(DepositReviewService::class)->reject($record, 'dashboard_widget');
                        Notification::make()->title('تم رفض الإيداع').danger()->send();
                    }),
            ])
            ->paginated(false);
    }
}

