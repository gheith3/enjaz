<?php

namespace App\Filament\Resources\PublicFigures\Pages;

use App\Filament\Resources\PublicFigures\PublicFigureResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPublicFigure extends ViewRecord
{
    protected static string $resource = PublicFigureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
