<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages\ManageReferrals;
use App\Models\Referral;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ReferralResource extends AdminResource
{
    protected static ?string $model = Referral::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'المستخدمون';
    protected static ?string $navigationLabel = 'الإحالات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('referrer_user_id')->required(),
            Forms\Components\TextInput::make('referred_user_id')->required(),
            Forms\Components\TextInput::make('reward_referrer')->numeric()->required(),
            Forms\Components\TextInput::make('reward_referred')->numeric()->required(),
            Forms\Components\Select::make('status')
                ->options(['pending' => 'Pending', 'done' => 'Done', 'cancelled' => 'Cancelled'])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('referrer_user_id')->label('Referrer'),
                Tables\Columns\TextColumn::make('referred_user_id')->label('Referred'),
                Tables\Columns\TextColumn::make('reward_referrer'),
                Tables\Columns\TextColumn::make('reward_referred'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReferrals::route('/'),
        ];
    }
}

