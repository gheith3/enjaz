<?php

namespace App\Filament\Resources\AchievementMainTypes\Pages;

use App\Filament\Resources\AchievementMainTypes\AchievementMainTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAchievementMainType extends EditRecord
{
    protected static string $resource = AchievementMainTypeResource::class;

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
