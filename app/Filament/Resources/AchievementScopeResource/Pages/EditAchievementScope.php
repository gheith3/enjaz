<?php

namespace App\Filament\Resources\AchievementScopeResource\Pages;

use App\Filament\Resources\AchievementScopeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAchievementScope extends EditRecord
{
    protected static string $resource = AchievementScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
