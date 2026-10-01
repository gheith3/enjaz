<?php

namespace App\Filament\Resources\AchievementMainTypeResource\Pages;

use App\Filament\Resources\AchievementMainTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAchievementMainType extends ViewRecord
{
    protected static string $resource = AchievementMainTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
