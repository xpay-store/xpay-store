<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DesignSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?string $navigationLabel = 'التصميم';
    protected static string $view = 'filament.pages.design-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $s = Setting::general();
        $this->form->fill([
            'site_name' => $s->site_name ?? 'XPayStore',
            'logo_url' => $s->logo_url ?? '',
            'primary_color' => $s->primary_color ?? '#16a34a',
            'secondary_color' => $s->secondary_color ?? '#0f172a',
            'enable_dark_mode' => (bool) ($s->enable_dark_mode ?? true),
            'card_style' => $s->card_style ?? 'rounded',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('site_name')->required(),
            TextInput::make('logo_url'),
            ColorPicker::make('primary_color')->required(),
            ColorPicker::make('secondary_color')->required(),
            Toggle::make('enable_dark_mode')->label('تفعيل الوضع الليلي'),
            TextInput::make('card_style')->helperText('rounded / sharp'),
        ])->statePath('data');
    }

    public function save(): void
    {
        $s = Setting::general();
        $s->fill($this->form->getState());
        $s->save();
        Notification::make()->title('تم حفظ إعدادات التصميم').success()->send();
    }

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->role ?? null) === 'admin';
    }
}

