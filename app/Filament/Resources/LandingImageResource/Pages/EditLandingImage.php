<?php

namespace App\Filament\Resources\LandingImageResource\Pages;

use App\Filament\Resources\LandingImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingImage extends EditRecord
{
    protected static string $resource = LandingImageResource::class;

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
