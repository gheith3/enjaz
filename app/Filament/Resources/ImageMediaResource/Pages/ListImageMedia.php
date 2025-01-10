<?php

namespace App\Filament\Resources\ImageMediaResource\Pages;

use App\Filament\Resources\ImageMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImageMedia extends ListRecords
{
    protected static string $resource = ImageMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
