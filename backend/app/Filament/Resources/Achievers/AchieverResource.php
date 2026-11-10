<?php

namespace App\Filament\Resources\Achievers;

use App\Filament\Resources\Achievers\Pages\CreateAchiever;
use App\Filament\Resources\Achievers\Pages\EditAchiever;
use App\Filament\Resources\Achievers\Pages\ListAchievers;
use App\Filament\Resources\Achievers\RelationManagers\AchievementsRelationManager;
use App\Filament\Resources\Achievers\RelationManagers\GalleryRelationManager;
use App\Filament\Resources\Achievers\RelationManagers\JourneyItemsRelationManager;
use App\Filament\Resources\Achievers\RelationManagers\OrganizationsRelationManager;
use App\Filament\Resources\Achievers\RelationManagers\QuotesRelationManager;
use App\Filament\Resources\Achievers\RelationManagers\SocialLinksRelationManager;
use App\Filament\Resources\Achievers\Schemas\AchieverForm;
use App\Filament\Resources\Achievers\Tables\AchieversTable;
use App\Models\Achiever;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AchieverResource extends Resource
{
    protected static ?string $model = Achiever::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'منجز';

    protected static ?string $pluralModelLabel = 'المنجزون';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AchieverForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchieversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AchievementsRelationManager::class,
            OrganizationsRelationManager::class,
            JourneyItemsRelationManager::class,
            QuotesRelationManager::class,
            SocialLinksRelationManager::class,
            GalleryRelationManager::class,
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAchievers::route('/'),
            'create' => CreateAchiever::route('/create'),
            'edit' => EditAchiever::route('/{record}/edit'),
        ];
    }
}
