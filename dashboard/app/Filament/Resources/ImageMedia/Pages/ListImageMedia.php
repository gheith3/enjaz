<?php

namespace App\Filament\Resources\ImageMedia\Pages;

use App\Filament\Resources\ImageMedia\ImageMediaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImageMedia extends ListRecords
{
    protected static string $resource = ImageMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
