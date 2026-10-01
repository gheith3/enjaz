<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactUsMessages\Pages;

use App\Filament\Resources\ContactUsMessages\ContactUsMessageResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContactUsMessage extends ViewRecord
{
    protected static string $resource = ContactUsMessageResource::class;
}
