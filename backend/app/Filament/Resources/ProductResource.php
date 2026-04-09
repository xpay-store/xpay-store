<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\ManageProducts;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends AdminResource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'الفئات والمنتجات';
    protected static ?string $navigationLabel = 'إدارة المنتجات';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('provider_product_id')->label('Provider Product ID'),
            Forms\Components\TextInput::make('provider_id')->label('Provider ID'),
            Forms\Components\TextInput::make('category_id')->label('Category ID'),
            Forms\Components\TextInput::make('price.USD')->numeric()->required(),
            Forms\Components\TextInput::make('price.SYP')->numeric()->required(),
            Forms\Components\TextInput::make('profit_percent')->numeric()->required(),
            Forms\Components\Select::make('product_type')->options(['amount' => 'Amount', 'package' => 'Package'])->required(),
            Forms\Components\Toggle::make('available')->default(true),
            Forms\Components\TextInput::make('image'),
            Forms\Components\KeyValue::make('params'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('price.USD')->label('USD')->sortable(),
                Tables\Columns\TextColumn::make('profit_percent')->label('Profit %'),
                Tables\Columns\IconColumn::make('available')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->since(),
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
            'index' => ManageProducts::route('/'),
        ];
    }
}

