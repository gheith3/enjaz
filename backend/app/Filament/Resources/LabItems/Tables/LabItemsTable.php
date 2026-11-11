<?php

namespace App\Filament\Resources\LabItems\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use App\Models\LabItem;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LabItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('happened_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->collection('cover')->conversion('card')->label('')->height(40)->width(64),
                TextColumn::make('title')->label('العنوان')->searchable(query: TranslatableColumn::search('title'))->limit(60)->wrap(),
                TextColumn::make('kind')->label('النوع')->formatStateUsing(fn (string $state) => LabItem::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('who')->label('المبتكر / الباحث')->limit(40)->toggleable(),
                TextColumn::make('medal')->label('الميدالية')->formatStateUsing(fn (?string $state) => LabItem::MEDALS[$state] ?? '')->toggleable(),
                TextColumn::make('happened_at')->label('التاريخ')->date('Y-m')->sortable(),
                ContentTable::publishedColumn(),
            ])
            ->filters([
                SelectFilter::make('kind')->label('النوع')->options(LabItem::KINDS),
                ...ContentTable::filters(),
            ])
            ->recordActions([EditAction::make(), ContentTable::openOnSite('lab.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
