<?php

namespace App\Filament\Resources\SubAchievementTypeResource\Pages;

use App\Filament\Resources\SubAchievementTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubAchievementType extends EditRecord
{
    protected static string $resource = SubAchievementTypeResource::class;

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
