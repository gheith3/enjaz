<?php

namespace App\Filament\Resources\AwardFeatures\Pages;

use App\Filament\Resources\AwardFeatures\AwardFeatureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAwardFeature extends EditRecord
{
    protected static string $resource = AwardFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
