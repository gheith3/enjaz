<?php

namespace App\Filament\Resources\VideoMedia\Pages;

use App\Filament\Resources\VideoMedia\VideoMediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVideoMedia extends CreateRecord
{
    protected static string $resource = VideoMediaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
