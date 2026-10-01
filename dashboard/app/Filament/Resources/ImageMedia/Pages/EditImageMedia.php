<?php

namespace App\Filament\Resources\ImageMedia\Pages;

use App\Filament\Resources\ImageMedia\ImageMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditImageMedia extends EditRecord
{
    protected static string $resource = ImageMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
