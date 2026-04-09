<?php

namespace App\Filament\Resources\OrderMessageTemplateResource\Pages;

use App\Filament\Resources\OrderMessageTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOrderMessageTemplates extends ManageRecords
{
    protected static string $resource = OrderMessageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

