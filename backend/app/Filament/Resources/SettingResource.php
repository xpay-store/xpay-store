<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages\ManageSettings;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationGroup = 'المدفوعات والإعدادات';
    protected static ?string $navigationLabel = 'الإعدادات العامة';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('_id')->disabled(),
            Forms\Components\TextInput::make('support_whatsapp'),
            Forms\Components\TextInput::make('sham_cash_wallet'),
            Forms\Components\TextInput::make('binance_pay_id'),
            Forms\Components\TextInput::make('binance_memo'),
            Forms\Components\TextInput::make('usd_to_syp_rate')->numeric(),
            Forms\Components\TextInput::make('min_deposit_usd')->numeric(),
            Forms\Components\Textarea::make('store_notice'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('_id'),
            Tables\Columns\TextColumn::make('support_whatsapp'),
            Tables\Columns\TextColumn::make('usd_to_syp_rate'),
            Tables\Columns\TextColumn::make('min_deposit_usd'),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSettings::route('/')];
    }
}

