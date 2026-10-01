<?php

namespace App\Filament\Resources\PublicFigures\Pages;

use App\Filament\Resources\PublicFigures\PublicFigureResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicFigure extends CreateRecord
{
    protected static string $resource = PublicFigureResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
