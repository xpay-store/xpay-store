<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderPriorityResource\Pages\ManageProviderPriorities;
use App\Models\Provider;
use App\Models\ProviderPriority;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderPriorityResource extends AdminResource
{
    protected static ?string $model = ProviderPriority::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-4';
    protected static ?string $navigationGroup = 'API';
    protected static ?string $navigationLabel = 'أولوية المزودين';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('provider_id')
                ->label('المزود')
                ->options(Provider::query()->pluck('name', '_id')->toArray())
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('priority')
                ->numeric()
                ->required()
                ->default(100),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('priority')
            ->reorderable('priority')
            ->columns([
                Tables\Columns\TextColumn::make('provider_id')->label('Provider ID'),
                Tables\Columns\TextColumn::make('priority')->sortable(),
                Tables\Columns\IconColumn::make('active')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProviderPriorities::route('/'),
        ];
    }
}

