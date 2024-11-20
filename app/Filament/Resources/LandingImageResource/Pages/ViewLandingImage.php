<?php

namespace App\Filament\Resources\LandingImageResource\Pages;

use App\Filament\Resources\LandingImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLandingImage extends ViewRecord
{
    protected static string $resource = LandingImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
