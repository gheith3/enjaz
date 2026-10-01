<?php

namespace App\Filament\Resources\AchievementMainTypeResource\Pages;

use App\Filament\Resources\AchievementMainTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAchievementMainType extends EditRecord
{
    protected static string $resource = AchievementMainTypeResource::class;

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
