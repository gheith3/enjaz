<?php

declare(strict_types=1);

namespace App\Filament\Resources\Achievements;

use App\Filament\Resources\Achievements\Pages\CreateAchievement;
use App\Filament\Resources\Achievements\Pages\EditAchievement;
use App\Filament\Resources\Achievements\Pages\ListAchievements;
use App\Filament\Resources\Achievements\Pages\ViewAchievement;
use App\Filament\Resources\Achievements\Schemas\AchievementForm;
use App\Filament\Resources\Achievements\Schemas\AchievementInfolist;
use App\Filament\Resources\Achievements\Tables\AchievementsTable;
use App\Models\Achievement;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.achievements');
    }

    protected static ?int $navigationSort = 1;

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
        return AchievementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AchievementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchievementsTable::configure($table);
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
            'index' => ListAchievements::route('/'),
            'create' => CreateAchievement::route('/create'),
            'view' => ViewAchievement::route('/{record}'),
            'edit' => EditAchievement::route('/{record}/edit'),
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
        return __('achievements.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('achievements.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('achievements.model_label_plural');
    }
}
