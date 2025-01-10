<?php

namespace App\Filament\Resources\VideoMediaResource\Pages;

use App\Filament\Resources\VideoMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideoMedia extends ListRecords
{
    protected static string $resource = VideoMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
