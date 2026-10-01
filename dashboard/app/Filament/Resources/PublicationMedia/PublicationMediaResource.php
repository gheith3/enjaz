<?php

declare(strict_types=1);

namespace App\Filament\Resources\PublicationMedia;

use App\Filament\Resources\PublicationMedia\Pages\CreatePublicationMedia;
use App\Filament\Resources\PublicationMedia\Pages\EditPublicationMedia;
use App\Filament\Resources\PublicationMedia\Pages\ListPublicationMedia;
use App\Filament\Resources\PublicationMedia\Pages\ViewPublicationMedia;
use App\Filament\Resources\PublicationMedia\Schemas\PublicationMediaForm;
use App\Filament\Resources\PublicationMedia\Schemas\PublicationMediaInfolist;
use App\Filament\Resources\PublicationMedia\Tables\PublicationMediaTable;
use App\Models\PublicationMedia;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PublicationMediaResource extends Resource
{
    protected static ?string $model = PublicationMedia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.media');
    }

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title_ar';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return PublicationMediaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PublicationMediaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicationMediaTable::configure($table);
    }

    /**
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPublicationMedia::route('/'),
            'create' => CreatePublicationMedia::route('/create'),
            'view' => ViewPublicationMedia::route('/{record}'),
            'edit' => EditPublicationMedia::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title_ar', 'title_en'];
    }

    public static function getModelLabel(): string
    {
        return __('publication_media.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('publication_media.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('publication_media.model_label_plural');
    }
}
