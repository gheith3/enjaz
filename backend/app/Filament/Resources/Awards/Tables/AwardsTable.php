<?php

namespace App\Filament\Resources\Awards\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use App\Models\Award;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AwardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name')->label('الجائزة')->searchable(query: TranslatableColumn::search('name'))->sortable(query: TranslatableColumn::sort('name')),
                TextColumn::make('kind')->label('التصميم')->formatStateUsing(fn (string $state) => Award::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('editions_count')->counts('editions')->label('الدورات'),
                TextColumn::make('winners_count')->counts('winners')->label('الفائزون'),
                ContentTable::publishedColumn(),
            ])
            ->filters(ContentTable::filters())
            ->recordActions([EditAction::make(), ContentTable::openOnSite('awards.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
