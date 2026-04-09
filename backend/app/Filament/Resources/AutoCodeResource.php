<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoCodeResource\Pages\ManageAutoCodes;
use App\Models\AutoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class AutoCodeResource extends AdminResource
{
    protected static ?string $model = AutoCode::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'المنتجات المتقدمة';
    protected static ?string $navigationLabel = 'إدارة الأكواد التلقائية';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('product_id')->required(),
            Forms\Components\Textarea::make('code')->required()->rows(2),
            Forms\Components\Toggle::make('is_used')->disabled(),
            Forms\Components\TextInput::make('used_by_order_id')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_id')->label('Product ID')->searchable(),
                Tables\Columns\TextColumn::make('code')->limit(45)->searchable(),
                Tables\Columns\IconColumn::make('is_used')->label('Used')->boolean(),
                Tables\Columns\TextColumn::make('used_by_order_id')->label('Order ID')->toggleable(),
                Tables\Columns\TextColumn::make('used_at')->since()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_used')->label('الحالة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAutoCodes::route('/'),
        ];
    }
}

