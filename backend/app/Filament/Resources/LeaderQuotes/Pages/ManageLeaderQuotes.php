<?php

namespace App\Filament\Resources\LeaderQuotes\Pages;

use App\Filament\Resources\LeaderQuotes\LeaderQuoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLeaderQuotes extends ManageRecords
{
    protected static string $resource = LeaderQuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
