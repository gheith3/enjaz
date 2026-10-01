<?php

namespace App\Filament\Resources\PublicationMedia\Pages;

use App\Filament\Resources\PublicationMedia\PublicationMediaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublicationMedia extends ListRecords
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
