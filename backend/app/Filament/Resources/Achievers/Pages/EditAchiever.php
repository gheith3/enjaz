<?php

namespace App\Filament\Resources\Achievers\Pages;

use App\Filament\Resources\Achievers\AchieverResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAchiever extends EditRecord
{
    protected static string $resource = AchieverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
