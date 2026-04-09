<?php

namespace App\Filament\Pages;

use App\Models\Provider;
use App\Services\ProviderService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ImportProducts extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'المزودون';
    protected static ?string $navigationLabel = 'استيراد المنتجات';
    protected static string $view = 'filament.pages.import-products';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('provider_id')
                ->label('المزود')
                ->options(Provider::query()->pluck('name', '_id')->toArray())
                ->searchable(),
        ])->statePath('data');
    }

    public function runImport(): void
    {
        $providerId = (string) ($this->data['provider_id'] ?? '');
        $count = 0;
        if ($providerId !== '') {
            $provider = Provider::query()->find($providerId);
            if ($provider) {
                $count = app(ProviderService::class)->syncProvider($provider);
            }
        } else {
            $count = app(ProviderService::class)->syncFromMersalEnv();
        }

        Notification::make()->title("تمت المزامنة: {$count} عنصر").success()->send();
    }

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->role ?? null) === 'admin';
    }
}

