<?php

namespace App\Filament\Resources\SubAchievementTypes\Pages;

use App\Filament\Resources\SubAchievementTypes\SubAchievementTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSubAchievementType extends ViewRecord
{
    protected static string $resource = SubAchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
