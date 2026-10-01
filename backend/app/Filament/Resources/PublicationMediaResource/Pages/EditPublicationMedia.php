<?php

namespace App\Filament\Resources\PublicationMediaResource\Pages;

use App\Filament\Resources\PublicationMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPublicationMedia extends EditRecord
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
