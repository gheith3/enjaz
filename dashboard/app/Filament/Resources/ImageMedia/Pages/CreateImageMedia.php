<?php

namespace App\Filament\Resources\ImageMedia\Pages;

use App\Filament\Resources\ImageMedia\ImageMediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateImageMedia extends CreateRecord
{
    protected static string $resource = ImageMediaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
