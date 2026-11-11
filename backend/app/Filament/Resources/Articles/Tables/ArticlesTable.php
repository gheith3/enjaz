<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        $name = fn ($record) => $record->name;

        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->collection('cover')->conversion('card')->label('')->height(40)->width(64),
                TextColumn::make('title')->label('العنوان')->searchable(query: TranslatableColumn::search('title'))->limit(60)->wrap(),
                TextColumn::make('writer.name')->label('الكاتب'),
                TextColumn::make('category.name')->label('التصنيف')->badge(),
                TextColumn::make('issue.number')->label('العدد')->toggleable(),
                TextColumn::make('views')->label('القراءات')->sortable()->toggleable(),
                TextColumn::make('published_at')->label('التاريخ')->date('Y-m-d')->sortable(),
                ContentTable::publishedColumn(),
            ])
            ->filters([
                SelectFilter::make('writer_id')->label('الكاتب')->relationship('writer', 'name')->getOptionLabelFromRecordUsing($name)->searchable()->preload(),
                SelectFilter::make('article_category_id')->label('التصنيف')->relationship('category', 'name')->getOptionLabelFromRecordUsing($name)->preload(),
                ...ContentTable::filters(),
            ])
            ->recordActions([EditAction::make(), ContentTable::openOnSite('magazine.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
