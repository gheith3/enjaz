<?php

namespace App\Filament\Resources\AwardEditions\Pages;

use App\Filament\Resources\AwardEditions\AwardEditionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwardEditions extends ListRecords
{
    protected static string $resource = AwardEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
