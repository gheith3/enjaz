<?php

namespace App\Filament\Resources\AchievementScopeResource\Pages;

use App\Filament\Resources\AchievementScopeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAchievementScope extends ViewRecord
{
    protected static string $resource = AchievementScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
