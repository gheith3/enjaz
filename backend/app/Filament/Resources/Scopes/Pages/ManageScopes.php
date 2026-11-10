<?php

namespace App\Filament\Resources\Scopes\Pages;

use App\Filament\Resources\Scopes\ScopeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageScopes extends ManageRecords
{
    protected static string $resource = ScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
