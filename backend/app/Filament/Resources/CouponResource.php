<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages\ManageCoupons;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationGroup = 'المدفوعات والإعدادات';
    protected static ?string $navigationLabel = 'القسائم';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->required(),
            Forms\Components\TextInput::make('discount_percent')->numeric()->required(),
            Forms\Components\TextInput::make('max_uses')->numeric()->required(),
            Forms\Components\DateTimePicker::make('expires_at'),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->searchable(),
            Tables\Columns\TextColumn::make('discount_percent')->suffix('%'),
            Tables\Columns\TextColumn::make('used_count'),
            Tables\Columns\IconColumn::make('active')->boolean(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCoupons::route('/')];
    }
}

