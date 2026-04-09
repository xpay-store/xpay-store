<?php

namespace App\Filament\Resources\ProviderPriorityResource\Pages;

use App\Filament\Resources\ProviderPriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProviderPriorities extends ManageRecords
{
    protected static string $resource = ProviderPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

