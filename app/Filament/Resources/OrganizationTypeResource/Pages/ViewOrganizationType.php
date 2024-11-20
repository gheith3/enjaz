<?php

namespace App\Filament\Resources\OrganizationTypeResource\Pages;

use App\Filament\Resources\OrganizationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganizationType extends ViewRecord
{
    protected static string $resource = OrganizationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
