<?php

namespace App\Filament\Resources\OrganizationTypes\Pages;

use App\Filament\Resources\OrganizationTypes\OrganizationTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizationType extends CreateRecord
{
    protected static string $resource = OrganizationTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
