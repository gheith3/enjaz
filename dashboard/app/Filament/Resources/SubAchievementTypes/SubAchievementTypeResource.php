<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubAchievementTypes;

use App\Filament\Resources\SubAchievementTypes\Pages\CreateSubAchievementType;
use App\Filament\Resources\SubAchievementTypes\Pages\EditSubAchievementType;
use App\Filament\Resources\SubAchievementTypes\Pages\ListSubAchievementTypes;
use App\Filament\Resources\SubAchievementTypes\Pages\ViewSubAchievementType;
use App\Filament\Resources\SubAchievementTypes\Schemas\SubAchievementTypeForm;
use App\Filament\Resources\SubAchievementTypes\Schemas\SubAchievementTypeInfolist;
use App\Filament\Resources\SubAchievementTypes\Tables\SubAchievementTypesTable;
use App\Models\SubAchievementType;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubAchievementTypeResource extends Resource
{
    protected static ?string $model = SubAchievementType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.classifications');
    }

    protected static ?int $navigationSort = 3;

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
        return SubAchievementTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubAchievementTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubAchievementTypesTable::configure($table);
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
            'index' => ListSubAchievementTypes::route('/'),
            'create' => CreateSubAchievementType::route('/create'),
            'view' => ViewSubAchievementType::route('/{record}'),
            'edit' => EditSubAchievementType::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('sub_achievement_types.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sub_achievement_types.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('sub_achievement_types.model_label_plural');
    }
}
