<?php

namespace App\Filament\Resources\AwardWinners\Pages;

use App\Filament\Resources\AwardWinners\AwardWinnerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwardWinners extends ListRecords
{
    protected static string $resource = AwardWinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
