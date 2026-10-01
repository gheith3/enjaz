<?php

namespace App\Filament\Resources\VideoMediaResource\Pages;

use App\Filament\Resources\VideoMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideoMedia extends EditRecord
{
    protected static string $resource = VideoMediaResource::class;

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
