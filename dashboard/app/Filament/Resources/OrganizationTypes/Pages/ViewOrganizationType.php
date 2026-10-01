<?php

namespace App\Filament\Resources\OrganizationTypes\Pages;

use App\Filament\Resources\OrganizationTypes\OrganizationTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganizationType extends ViewRecord
{
    protected static string $resource = OrganizationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
