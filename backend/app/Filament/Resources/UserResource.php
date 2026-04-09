<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\ManageUsers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends AdminResource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'المستخدمون';
    protected static ?string $navigationLabel = 'إدارة المستخدمين';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('username'),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\TextInput::make('telegram_id')->numeric(),
            Forms\Components\Select::make('role')
                ->options(['user' => 'User', 'agent' => 'Agent', 'admin' => 'Admin'])
                ->required(),
            Forms\Components\Toggle::make('is_banned'),
            Forms\Components\TextInput::make('balance.USD')->numeric(),
            Forms\Components\TextInput::make('balance.SYP')->numeric(),
            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')->badge(),
                Tables\Columns\IconColumn::make('is_banned')->boolean(),
                Tables\Columns\TextColumn::make('balance.USD')->label('USD'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}

