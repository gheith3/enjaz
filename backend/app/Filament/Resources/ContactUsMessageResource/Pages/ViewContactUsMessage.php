<?php

namespace App\Filament\Resources\ContactUsMessageResource\Pages;

use App\Filament\Resources\ContactUsMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactUsMessage extends ViewRecord
{
    protected static string $resource = ContactUsMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
