<?php

namespace App\Filament\Resources\AchievementScopes\Pages;

use App\Filament\Resources\AchievementScopes\AchievementScopeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAchievementScope extends ViewRecord
{
    protected static string $resource = AchievementScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
