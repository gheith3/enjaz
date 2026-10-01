<?php

namespace App\Filament\Resources\AchievementMainTypes\Pages;

use App\Filament\Resources\AchievementMainTypes\AchievementMainTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAchievementMainType extends CreateRecord
{
    protected static string $resource = AchievementMainTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
