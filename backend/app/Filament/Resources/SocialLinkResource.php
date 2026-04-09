<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages\ManageSocialLinks;
use App\Models\SocialLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class SocialLinkResource extends AdminResource
{
    protected static ?string $model = SocialLink::class;
    protected static ?string $navigationGroup = 'المدفوعات والإعدادات';
    protected static ?string $navigationLabel = 'روابط التواصل';
    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('platform')->required(),
            Forms\Components\TextInput::make('url')->required()->url(),
            Forms\Components\TextInput::make('order')->numeric()->default(0),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('platform')->searchable(),
            Tables\Columns\TextColumn::make('url')->limit(40),
            Tables\Columns\TextColumn::make('order'),
            Tables\Columns\IconColumn::make('active')->boolean(),
        ])->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSocialLinks::route('/')];
    }
}

