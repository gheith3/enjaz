<?php

namespace App\Filament\Resources\PublicationMediaResource\Pages;

use App\Filament\Resources\PublicationMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPublicationMedia extends ViewRecord
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
