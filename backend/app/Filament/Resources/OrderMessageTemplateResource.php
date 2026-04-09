<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderMessageTemplateResource\Pages\ManageOrderMessageTemplates;
use App\Models\OrderMessageTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class OrderMessageTemplateResource extends AdminResource
{
    protected static ?string $model = OrderMessageTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'رسائل الطلب والرد';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')
                ->required()
                ->helperText('مثال: order_success, order_failed, deposit_approved, deposit_rejected'),
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Textarea::make('body')
                ->required()
                ->rows(8)
                ->helperText('يمكنك استخدام placeholders مثل {order_number} و {amount_usd}.'),
            Forms\Components\Toggle::make('active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('key')->searchable(),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\IconColumn::make('active')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->since(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrderMessageTemplates::route('/'),
        ];
    }
}

