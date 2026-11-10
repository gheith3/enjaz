<?php

namespace App\Filament\Resources\Awards\RelationManagers;

use App\Filament\Resources\AwardEditions\AwardEditionResource;
use App\Models\AwardEdition;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Editions of the award; each opens its own page (details, winners, photos). */
class EditionsRelationManager extends RelationManager
{
    protected static string $relationship = 'editions';

    protected static ?string $title = 'الدورات والفائزون';

    protected static ?string $modelLabel = 'دورة';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                TextColumn::make('number')->label('الرقم'),
                TextColumn::make('year')->label('السنة'),
                TextColumn::make('type')->label('النوع')->formatStateUsing(fn (?string $state) => AwardEdition::TYPES[$state] ?? $state)->badge(),
                TextColumn::make('title')->label('العنوان')->limit(40),
                TextColumn::make('winners_count')->counts('winners')->label('الفائزون'),
                IconColumn::make('is_current')->label('الجارية')->boolean(),
            ])
            ->headerActions([
                Action::make('create')->label('إضافة دورة')->icon('heroicon-o-plus')
                    ->url(fn () => AwardEditionResource::getUrl('create', ['award' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordUrl(fn (AwardEdition $record) => AwardEditionResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('edit')->label('فتح')->icon('heroicon-o-pencil-square')->url(fn (AwardEdition $record) => AwardEditionResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }
}
