<?php

namespace App\Filament\Resources\ContactTopics\Pages;

use App\Filament\Resources\ContactTopics\ContactTopicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageContactTopics extends ManageRecords
{
    protected static string $resource = ContactTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
