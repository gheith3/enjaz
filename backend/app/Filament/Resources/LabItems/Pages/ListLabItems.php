<?php

namespace App\Filament\Resources\LabItems\Pages;

use App\Filament\Resources\LabItems\LabItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLabItems extends ListRecords
{
    protected static string $resource = LabItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
