<?php

namespace App\Filament\Resources\AchievementMainTypes\Pages;

use App\Filament\Resources\AchievementMainTypes\AchievementMainTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAchievementMainType extends ViewRecord
{
    protected static string $resource = AchievementMainTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
