<?php

namespace App\Filament\Resources\OrganizationTypes\Pages;

use App\Filament\Resources\OrganizationTypes\OrganizationTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationTypes extends ListRecords
{
    protected static string $resource = OrganizationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
