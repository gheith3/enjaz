<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrganizationTypes;

use App\Filament\Resources\OrganizationTypes\Pages\CreateOrganizationType;
use App\Filament\Resources\OrganizationTypes\Pages\EditOrganizationType;
use App\Filament\Resources\OrganizationTypes\Pages\ListOrganizationTypes;
use App\Filament\Resources\OrganizationTypes\Pages\ViewOrganizationType;
use App\Filament\Resources\OrganizationTypes\Schemas\OrganizationTypeForm;
use App\Filament\Resources\OrganizationTypes\Schemas\OrganizationTypeInfolist;
use App\Filament\Resources\OrganizationTypes\Tables\OrganizationTypesTable;
use App\Models\OrganizationType;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationTypeResource extends Resource
{
    protected static ?string $model = OrganizationType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title_ar';

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.classifications');
    }

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
        return OrganizationTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationTypesTable::configure($table);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationTypes::route('/'),
            'create' => CreateOrganizationType::route('/create'),
            'view' => ViewOrganizationType::route('/{record}'),
            'edit' => EditOrganizationType::route('/{record}/edit'),
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
        return __('organization_types.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('organization_types.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('organization_types.model_label_plural');
    }
}
