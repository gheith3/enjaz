<?php

namespace App\Filament\Resources\LabItems;

use App\Filament\Resources\Achievements\RelationManagers\SourcesRelationManager;
use App\Filament\Resources\LabItems\Pages\CreateLabItem;
use App\Filament\Resources\LabItems\Pages\EditLabItem;
use App\Filament\Resources\LabItems\Pages\ListLabItems;
use App\Filament\Resources\LabItems\Schemas\LabItemForm;
use App\Filament\Resources\LabItems\Tables\LabItemsTable;
use App\Models\LabItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

/** المختبر: inventions and scientific publications (science.html, lab-all.html, lab-item.html). */
class LabItemResource extends Resource
{
    protected static ?string $model = LabItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'عنصر المختبر';

    protected static ?string $pluralModelLabel = 'المختبر';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LabItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LabItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [RelationManagers\FactsRelationManager::class, SourcesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLabItems::route('/'),
            'create' => CreateLabItem::route('/create'),
            'edit' => EditLabItem::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
