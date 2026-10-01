<?php

declare(strict_types=1);

namespace App\Filament\Resources\AchievementScopes;

use App\Filament\Resources\AchievementScopes\Pages\CreateAchievementScope;
use App\Filament\Resources\AchievementScopes\Pages\EditAchievementScope;
use App\Filament\Resources\AchievementScopes\Pages\ListAchievementScopes;
use App\Filament\Resources\AchievementScopes\Pages\ViewAchievementScope;
use App\Filament\Resources\AchievementScopes\Schemas\AchievementScopeForm;
use App\Filament\Resources\AchievementScopes\Schemas\AchievementScopeInfolist;
use App\Filament\Resources\AchievementScopes\Tables\AchievementScopesTable;
use App\Models\AchievementScope;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AchievementScopeResource extends Resource
{
    protected static ?string $model = AchievementScope::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.classifications');
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
        return AchievementScopeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AchievementScopeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchievementScopesTable::configure($table);
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
            'index' => ListAchievementScopes::route('/'),
            'create' => CreateAchievementScope::route('/create'),
            'view' => ViewAchievementScope::route('/{record}'),
            'edit' => EditAchievementScope::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('achievement_scopes.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('achievement_scopes.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('achievement_scopes.model_label_plural');
    }
}
