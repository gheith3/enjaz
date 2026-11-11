<?php

namespace App\Filament\Resources\LabItems\Pages;

use App\Filament\Resources\LabItems\LabItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLabItem extends EditRecord
{
    protected static string $resource = LabItemResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), ForceDeleteAction::make(), RestoreAction::make()];
    }
}
