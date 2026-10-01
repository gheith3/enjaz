<?php

namespace App\Filament\Resources\PublicationMedia\Pages;

use App\Filament\Resources\PublicationMedia\PublicationMediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicationMedia extends CreateRecord
{
    protected static string $resource = PublicationMediaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
