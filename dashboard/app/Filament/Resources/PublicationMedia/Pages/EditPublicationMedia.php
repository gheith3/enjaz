<?php

namespace App\Filament\Resources\PublicationMedia\Pages;

use App\Filament\Resources\PublicationMedia\PublicationMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPublicationMedia extends EditRecord
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
