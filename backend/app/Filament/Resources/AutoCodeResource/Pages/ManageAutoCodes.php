<?php

namespace App\Filament\Resources\AutoCodeResource\Pages;

use App\Filament\Resources\AutoCodeResource;
use App\Models\AutoCode;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageAutoCodes extends ManageRecords
{
    protected static string $resource = AutoCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('bulkImport')
                ->label('استيراد أكواد دفعة واحدة')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    TextInput::make('product_id')->required()->label('Product ID'),
                    Textarea::make('codes')
                        ->required()
                        ->rows(8)
                        ->label('Codes (one per line)'),
                ])
                ->action(function (array $data) {
                    $productId = (string) $data['product_id'];
                    $codesRaw = preg_split('/\r\n|\r|\n/', (string) $data['codes']) ?: [];
                    $count = 0;
                    foreach ($codesRaw as $line) {
                        $code = trim($line);
                        if ($code === '') {
                            continue;
                        }
                        AutoCode::query()->create([
                            'product_id' => $productId,
                            'code' => $code,
                            'is_used' => false,
                            'used_by_order_id' => null,
                            'used_at' => null,
                        ]);
                        $count++;
                    }
                    Notification::make()->title("تم استيراد {$count} كود").success()->send();
                }),
        ];
    }
}

