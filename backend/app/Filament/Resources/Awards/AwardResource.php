<?php

namespace App\Filament\Resources\Awards;

use App\Filament\Resources\Achievements\RelationManagers\SourcesRelationManager;
use App\Filament\Resources\Awards\Pages\CreateAward;
use App\Filament\Resources\Awards\Pages\EditAward;
use App\Filament\Resources\Awards\Pages\ListAwards;
use App\Filament\Resources\Awards\Schemas\AwardForm;
use App\Filament\Resources\Awards\Tables\AwardsTable;
use App\Models\Award;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AwardResource extends Resource
{
    protected static ?string $model = Award::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'جائزة';

    protected static ?string $pluralModelLabel = 'الجوائز';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AwardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AwardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EditionsRelationManager::class,
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\FactsRelationManager::class,
            RelationManagers\FeaturesRelationManager::class,
            SourcesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAwards::route('/'),
            'create' => CreateAward::route('/create'),
            'edit' => EditAward::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
