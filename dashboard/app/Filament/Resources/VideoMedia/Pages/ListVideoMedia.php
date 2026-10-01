<?php

namespace App\Filament\Resources\VideoMedia\Pages;

use App\Filament\Resources\VideoMedia\VideoMediaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVideoMedia extends ListRecords
{
    protected static string $resource = VideoMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
