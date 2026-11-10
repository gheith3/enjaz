<?php

namespace App\Filament\Resources\News\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use App\Models\News;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('happened_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->collection('cover')->conversion('card')->label('')->height(40)->width(64),
                TextColumn::make('title')->label('العنوان')->searchable(query: TranslatableColumn::search('title'))->limit(60)->wrap(),
                TextColumn::make('organization.name')->label('المؤسسة')->toggleable(),
                TextColumn::make('kind')->label('النوع')->formatStateUsing(fn (string $state) => News::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('happened_at')->label('التاريخ')->date('Y-m-d')->sortable(),
                ContentTable::publishedColumn(),
            ])
            ->filters([
                SelectFilter::make('organization_id')->label('المؤسسة')->relationship('organization', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload(),
                SelectFilter::make('kind')->label('النوع')->options(News::KINDS),
                ...ContentTable::filters(),
            ])
            ->recordActions([EditAction::make(), ContentTable::openOnSite('news.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
