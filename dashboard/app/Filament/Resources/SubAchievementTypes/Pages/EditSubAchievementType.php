<?php

namespace App\Filament\Resources\SubAchievementTypes\Pages;

use App\Filament\Resources\SubAchievementTypes\SubAchievementTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSubAchievementType extends EditRecord
{
    protected static string $resource = SubAchievementTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
