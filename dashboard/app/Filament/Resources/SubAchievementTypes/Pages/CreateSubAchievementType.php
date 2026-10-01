<?php

namespace App\Filament\Resources\SubAchievementTypes\Pages;

use App\Filament\Resources\SubAchievementTypes\SubAchievementTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubAchievementType extends CreateRecord
{
    protected static string $resource = SubAchievementTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
