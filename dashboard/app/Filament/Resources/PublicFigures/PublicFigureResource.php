<?php

declare(strict_types=1);

namespace App\Filament\Resources\PublicFigures;

use App\Filament\Resources\PublicFigures\Pages\CreatePublicFigure;
use App\Filament\Resources\PublicFigures\Pages\EditPublicFigure;
use App\Filament\Resources\PublicFigures\Pages\ListPublicFigures;
use App\Filament\Resources\PublicFigures\Pages\ViewPublicFigure;
use App\Filament\Resources\PublicFigures\Schemas\PublicFigureForm;
use App\Filament\Resources\PublicFigures\Schemas\PublicFigureInfolist;
use App\Filament\Resources\PublicFigures\Tables\PublicFiguresTable;
use App\Models\PublicFigure;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Public Figure Resource for managing public figures (المنجزون).
 */
class PublicFigureResource extends Resource
{
    protected static ?string $model = PublicFigure::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.people');
    }

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name_ar';

    /**
     * Get the navigation badge count.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    /**
     * Configure the form schema.
     */
    public static function form(Schema $schema): Schema
    {
        return PublicFigureForm::configure($schema);
    }

    /**
     * Configure the infolist schema.
     */
    public static function infolist(Schema $schema): Schema
    {
        return PublicFigureInfolist::configure($schema);
    }

    /**
     * Configure the table.
     */
    public static function table(Table $table): Table
    {
        return PublicFiguresTable::configure($table);
    }

    /**
     * Get the resource relations.
     *
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Get the resource pages.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPublicFigures::route('/'),
            'create' => CreatePublicFigure::route('/create'),
            'view' => ViewPublicFigure::route('/{record}'),
            'edit' => EditPublicFigure::route('/{record}/edit'),
        ];
    }

    /**
     * Get the globally searchable attributes.
     *
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name_ar', 'name_en', 'headline_ar', 'headline_en'];
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('public_figures.model_label');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('public_figures.model_label_plural');
    }

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('public_figures.model_label_plural');
    }
}
