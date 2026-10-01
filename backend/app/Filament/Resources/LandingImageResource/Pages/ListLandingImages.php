<?php

namespace App\Filament\Resources\LandingImageResource\Pages;

use App\Filament\Resources\LandingImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingImages extends ListRecords
{
    protected static string $resource = LandingImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
