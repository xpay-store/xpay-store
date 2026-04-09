<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages\ManageProviders;
use App\Models\Provider;
use App\Services\ProviderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderResource extends AdminResource
{
    protected static ?string $model = Provider::class;
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'API';
    protected static ?string $navigationLabel = 'إدارة المزودين (A2Z / SMM)';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('type')->options(['a2z' => 'a2z', 'smm' => 'smm', 'custom' => 'custom'])->required(),
            Forms\Components\TextInput::make('api_url')->required()->url(),
            Forms\Components\TextInput::make('api_token')->password(),
            Forms\Components\Toggle::make('active')->default(true),
            Forms\Components\TextInput::make('balance')->numeric()->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('balance'),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('last_sync')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('sync')->action(fn ($record) => app(ProviderService::class)->syncProvider($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProviders::route('/'),
        ];
    }
}

