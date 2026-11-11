<?php

namespace App\Filament\Resources\PageSections\Pages;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\PageSections\PageSectionResource;
use Filament\Resources\Pages\EditRecord;

class EditPageSection extends EditRecord
{
    protected static string $resource = PageSectionResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->page->title.' · '.$this->getRecord()->label();
    }

    public function getBreadcrumbs(): array
    {
        $page = $this->getRecord()->page;

        return [PageResource::getUrl('index') => 'صفحات الموقع', PageResource::getUrl('edit', ['record' => $page]) => $page->title, 'القسم'];
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }
}
