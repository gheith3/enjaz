<?php

namespace App\Filament\Resources\AwardWinners;

use App\Filament\Resources\Awards\RelationManagers\FactsRelationManager;
use App\Filament\Resources\AwardWinners\Schemas\AwardWinnerForm;
use App\Filament\Support\TranslatableColumn;
use App\Models\Award;
use App\Models\AwardEdition;
use App\Models\AwardWinner;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AwardWinnerResource extends Resource
{
    protected static ?string $model = AwardWinner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $modelLabel = 'فائز';

    protected static ?string $pluralModelLabel = 'فائزو الجوائز';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('award_edition_id')->label('الدورة')->relationship('edition', 'year')->required()->live()->preload()
                ->getOptionLabelFromRecordUsing(fn (AwardEdition $record) => $record->award?->name.' · '.$record->year),
            ...AwardWinnerForm::fields(fn (Get $get) => AwardEdition::query()->whereKey($get('award_edition_id'))->value('award_id')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection('photo')->label('')->circular()->height(36),
                TextColumn::make('name')->label('الفائز')->state(fn (AwardWinner $record) => $record->displayName())
                    ->searchable(query: TranslatableColumn::search('name')),
                TextColumn::make('edition.award.name')->label('الجائزة'),
                TextColumn::make('edition.year')->label('الدورة'),
                TextColumn::make('category.name')->label('الفرع / الفئة'),
                TextColumn::make('rank')->label('المركز'),
            ])
            ->filters([
                SelectFilter::make('award')->label('الجائزة')->options(fn () => Award::query()->get()->mapWithKeys(fn ($a) => [$a->id => $a->name]))
                    ->query(fn ($query, array $data) => $data['value'] ? $query->whereHas('edition', fn ($q) => $q->where('award_id', $data['value'])) : $query),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getRelations(): array
    {
        return [RelationManagers\DesignsRelationManager::class, RelationManagers\GalleryRelationManager::class, FactsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAwardWinners::route('/'),
            'create' => Pages\CreateAwardWinner::route('/create'),
            'edit' => Pages\EditAwardWinner::route('/{record}/edit'),
        ];
    }
}
