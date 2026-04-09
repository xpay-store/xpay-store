<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages\ManageNews;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class NewsResource extends AdminResource
{
    protected static ?string $model = News::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'شريط الأخبار';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'general' => 'عام',
                    'offer' => 'عرض خاص',
                    'alert' => 'تنبيه',
                    'service' => 'خدمة جديدة',
                ])
                ->default('general'),
            Forms\Components\Textarea::make('content')->required()->rows(4),
            Forms\Components\TextInput::make('image'),
            Forms\Components\Toggle::make('active')->default(true),
            Forms\Components\DateTimePicker::make('published_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageNews::route('/'),
        ];
    }
}

