<?php

namespace App\Filament\Resources\PublicFigureResource\Pages;

use App\Filament\Resources\PublicFigureResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPublicFigure extends ViewRecord
{
    protected static string $resource = PublicFigureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
