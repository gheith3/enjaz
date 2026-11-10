<?php

namespace App\Filament\Resources\AwardFeatures\Pages;

use App\Filament\Resources\AwardFeatures\AwardFeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwardFeatures extends ListRecords
{
    protected static string $resource = AwardFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
