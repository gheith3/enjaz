<?php

namespace App\Filament\Resources\ImageMedia\Pages;

use App\Filament\Resources\ImageMedia\ImageMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewImageMedia extends ViewRecord
{
    protected static string $resource = ImageMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
