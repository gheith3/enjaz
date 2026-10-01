<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactUsMessages\Pages;

use App\Filament\Resources\ContactUsMessages\ContactUsMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListContactUsMessages extends ListRecords
{
    protected static string $resource = ContactUsMessageResource::class;
}
