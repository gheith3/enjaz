<?php

namespace App\Filament\Resources\PublicationMediaResource\Pages;

use App\Filament\Resources\PublicationMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicationMedia extends ListRecords
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
