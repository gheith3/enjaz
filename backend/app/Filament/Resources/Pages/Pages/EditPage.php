<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')->label('عرض في الموقع')->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record->publicUrl(), shouldOpenInNewTab: true)
                ->visible(fn () => $this->record->publicUrl() !== null),
        ];
    }
}
