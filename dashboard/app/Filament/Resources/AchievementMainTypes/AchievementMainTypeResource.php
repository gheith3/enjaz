<?php

declare(strict_types=1);

namespace App\Filament\Resources\AchievementMainTypes;

use App\Filament\Resources\AchievementMainTypes\Pages\CreateAchievementMainType;
use App\Filament\Resources\AchievementMainTypes\Pages\EditAchievementMainType;
use App\Filament\Resources\AchievementMainTypes\Pages\ListAchievementMainTypes;
use App\Filament\Resources\AchievementMainTypes\Pages\ViewAchievementMainType;
use App\Filament\Resources\AchievementMainTypes\Schemas\AchievementMainTypeForm;
use App\Filament\Resources\AchievementMainTypes\Schemas\AchievementMainTypeInfolist;
use App\Filament\Resources\AchievementMainTypes\Tables\AchievementMainTypesTable;
use App\Models\AchievementMainType;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AchievementMainTypeResource extends Resource
{
    protected static ?string $model = AchievementMainType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.classifications');
    }

    protected static ?int $navigationSort = 2;

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
        return AchievementMainTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AchievementMainTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchievementMainTypesTable::configure($table);
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
            'index' => ListAchievementMainTypes::route('/'),
            'create' => CreateAchievementMainType::route('/create'),
            'view' => ViewAchievementMainType::route('/{record}'),
            'edit' => EditAchievementMainType::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('achievement_main_types.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('achievement_main_types.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('achievement_main_types.model_label_plural');
    }
}
