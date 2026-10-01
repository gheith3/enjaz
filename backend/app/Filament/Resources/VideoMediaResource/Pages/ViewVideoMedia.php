<?php

namespace App\Filament\Resources\VideoMediaResource\Pages;

use App\Filament\Resources\VideoMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVideoMedia extends ViewRecord
{
    protected static string $resource = VideoMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
