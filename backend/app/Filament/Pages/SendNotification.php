<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Services\TelegramBotService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SendNotification extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'أدوات متقدمة';
    protected static ?string $navigationLabel = 'إرسال إشعار';
    protected static string $view = 'filament.pages.send-notification';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'broadcast' => true,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Toggle::make('broadcast')->label('إرسال للجميع'),
            TextInput::make('telegram_id')->numeric()->label('Telegram ID (إذا ليس للجميع)'),
            TextInput::make('link')->label('رابط اختياري'),
            Textarea::make('message')->required()->label('نص الإشعار'),
        ])->statePath('data');
    }

    public function send(): void
    {
        $state = $this->form->getState();
        $message = (string) ($state['message'] ?? '');
        $link = trim((string) ($state['link'] ?? ''));
        if ($link !== '') {
            $message .= "\n".$link;
        }

        $service = app(TelegramBotService::class);
        $count = 0;

        if (($state['broadcast'] ?? false) === true) {
            foreach (User::query()->where('is_banned', false)->get() as $u) {
                if ($service->sendStoreUserMessage((int) $u->telegram_id, $message)) {
                    $count++;
                }
            }
        } else {
            $id = (int) ($state['telegram_id'] ?? 0);
            if ($id > 0 && $service->sendStoreUserMessage($id, $message)) {
                $count = 1;
            }
        }

        Notification::make()->title("تم إرسال {$count} إشعار").success()->send();
    }
}

