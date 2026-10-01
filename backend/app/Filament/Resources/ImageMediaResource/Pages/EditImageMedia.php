<?php

namespace App\Filament\Resources\ImageMediaResource\Pages;

use App\Filament\Resources\ImageMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImageMedia extends EditRecord
{
    protected static string $resource = ImageMediaResource::class;

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
