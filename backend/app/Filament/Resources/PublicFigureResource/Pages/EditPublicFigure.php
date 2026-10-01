<?php

namespace App\Filament\Resources\PublicFigureResource\Pages;

use App\Filament\Resources\PublicFigureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPublicFigure extends EditRecord
{
    protected static string $resource = PublicFigureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
