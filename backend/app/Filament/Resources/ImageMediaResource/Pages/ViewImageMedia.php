<?php

namespace App\Filament\Resources\ImageMediaResource\Pages;

use App\Filament\Resources\ImageMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewImageMedia extends ViewRecord
{
    protected static string $resource = ImageMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
