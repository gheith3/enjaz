<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        if (! $this->getRecord()->read_at) {
            $this->getRecord()->forceFill(['read_at' => now()])->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')->label('الرد بالبريد')->icon('heroicon-o-arrow-uturn-left')
                ->url(fn () => 'mailto:'.$this->getRecord()->email.'?subject='.rawurlencode('رد: '.$this->getRecord()->topic)),
            ContactMessageResource::downloadAction(),
            Action::make('unread')->label('تعليم كغير مقروءة')->icon('heroicon-o-envelope')->color('gray')
                ->action(fn () => $this->getRecord()->forceFill(['read_at' => null])->save()),
            DeleteAction::make(),
        ];
    }
}
