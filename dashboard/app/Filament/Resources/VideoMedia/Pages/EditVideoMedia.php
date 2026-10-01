<?php

namespace App\Filament\Resources\VideoMedia\Pages;

use App\Filament\Resources\VideoMedia\VideoMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVideoMedia extends EditRecord
{
    protected static string $resource = VideoMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
