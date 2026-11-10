<?php

namespace App\Filament\Resources\AwardEditions\Pages;

use App\Filament\Resources\AwardEditions\AwardEditionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAwardEdition extends EditRecord
{
    protected static string $resource = AwardEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
