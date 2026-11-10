<?php

namespace App\Filament\Resources\Achievers\Pages;

use App\Filament\Resources\Achievers\AchieverResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAchievers extends ListRecords
{
    protected static string $resource = AchieverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
