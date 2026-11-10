<?php

namespace App\Filament\Resources\Achievements\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use App\Models\Achievement;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AchievementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('achieved_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->collection('cover')->conversion('card')->label('')->height(40)->width(64),
                TextColumn::make('title')->label('الإنجاز')->searchable(query: TranslatableColumn::search('title'))->limit(60)->wrap(),
                TextColumn::make('achievers.name')->label('المنجز')->badge()->limitList(2),
                TextColumn::make('field.name')->label('المجال')->toggleable(),
                TextColumn::make('scope.name')->label('المستوى')->badge()->color('gray'),
                TextColumn::make('achieved_at')->label('التاريخ')->sortable()->state(fn (Achievement $record) => $record->dateLabel()),
                IconColumn::make('is_featured')->label('لوحة الشرف')->boolean()->toggleable(),
                ContentTable::publishedColumn(),
            ])
            ->filters([
                SelectFilter::make('field_id')->label('المجال')->relationship('field', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                SelectFilter::make('scope_id')->label('المستوى')->relationship('scope', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                SelectFilter::make('achievers')->label('المنجز')->relationship('achievers', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload(),
                ...ContentTable::filters(),
            ])
            ->recordActions([EditAction::make(), ContentTable::openOnSite('achievements.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
