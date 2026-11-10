<?php

namespace App\Filament\Resources\AwardWinners\Pages;

use App\Filament\Resources\AwardWinners\AwardWinnerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAwardWinner extends EditRecord
{
    protected static string $resource = AwardWinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
