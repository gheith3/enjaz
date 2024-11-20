<?php

namespace App\Filament\Resources\SubAchievementTypeResource\Pages;

use App\Filament\Resources\SubAchievementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubAchievementType extends ViewRecord
{
    protected static string $resource = SubAchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
