<?php

namespace App\Filament\Resources\AchievementScopes\Pages;

use App\Filament\Resources\AchievementScopes\AchievementScopeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAchievementScope extends CreateRecord
{
    protected static string $resource = AchievementScopeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
