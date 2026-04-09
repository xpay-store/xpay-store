<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages\ManageDeposits;
use App\Models\Deposit;
use App\Services\DepositReviewService;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class DepositResource extends AdminResource
{
    protected static ?string $model = Deposit::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'الطلبات والعمليات';
    protected static ?string $navigationLabel = 'الإيداع اليدوي';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('_id')->label('ID'),
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('amount.USD')->label('USD'),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('proof_image')->url()->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        app(DepositReviewService::class)->approve($record, 'filament');
                    })
                    ->color('success'),
                Tables\Actions\Action::make('reject')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        app(DepositReviewService::class)->reject($record, 'filament');
                    })
                    ->color('danger'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDeposits::route('/'),
        ];
    }
}

