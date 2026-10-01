<?php

namespace App\Filament\Resources\PublicFigures\Pages;

use App\Filament\Resources\PublicFigures\PublicFigureResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPublicFigure extends EditRecord
{
    protected static string $resource = PublicFigureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
