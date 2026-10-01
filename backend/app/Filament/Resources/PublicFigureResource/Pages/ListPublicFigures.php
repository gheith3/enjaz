<?php

namespace App\Filament\Resources\PublicFigureResource\Pages;

use App\Filament\Resources\PublicFigureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicFigures extends ListRecords
{
    protected static string $resource = PublicFigureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
