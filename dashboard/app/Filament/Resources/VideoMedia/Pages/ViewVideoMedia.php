<?php

namespace App\Filament\Resources\VideoMedia\Pages;

use App\Filament\Resources\VideoMedia\VideoMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVideoMedia extends ViewRecord
{
    protected static string $resource = VideoMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
