<?php

namespace App\Filament\Resources\AwardEditions\RelationManagers;

use App\Filament\Resources\AwardWinners\AwardWinnerResource;
use App\Filament\Resources\AwardWinners\Schemas\AwardWinnerForm;
use App\Models\AwardWinner;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Winners of the edition. Design images, extra photos and facts are on the winner's own page. */
class WinnersRelationManager extends RelationManager
{
    protected static string $relationship = 'winners';

    protected static ?string $title = 'الفائزون';

    protected static ?string $modelLabel = 'فائز';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(AwardWinnerForm::fields(fn () => $this->getOwnerRecord()->award_id));
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection('photo')->label('')->circular()->height(36),
                TextColumn::make('name')->label('الفائز')->state(fn (AwardWinner $record) => $record->displayName()),
                TextColumn::make('category.name')->label('الفرع / الفئة'),
                TextColumn::make('rank')->label('المركز'),
                TextColumn::make('work')->label('العمل')->limit(40),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                EditAction::make(),
                Action::make('page')->label('الصور والمعلومات')->icon('heroicon-o-photo')->url(fn (AwardWinner $record) => AwardWinnerResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }
}
