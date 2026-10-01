<?php

namespace App\Filament\Resources\PublicationMedia\Pages;

use App\Filament\Resources\PublicationMedia\PublicationMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPublicationMedia extends ViewRecord
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
