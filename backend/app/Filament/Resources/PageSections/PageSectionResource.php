<?php

namespace App\Filament\Resources\PageSections;

use App\Filament\Resources\PageSections\Pages\EditPageSection;
use App\Filament\Support\SectionSettings;
use App\Models\PageSection;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

/** A section of a fixed page: its texts and, for some sections, the records picked for it. Opened from the page's sections. */
class PageSectionResource extends Resource
{
    protected static ?string $model = PageSection::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'قسم';

    protected static ?string $pluralModelLabel = 'أقسام الصفحات';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(fn (?PageSection $record) => SectionSettings::schema($record?->key));
    }

    public static function getRelations(): array
    {
        return [RelationManagers\ItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return ['edit' => EditPageSection::route('/{record}/edit')];
    }
}
