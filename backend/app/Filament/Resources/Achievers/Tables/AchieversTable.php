<?php

namespace App\Filament\Resources\Achievers\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use App\Models\Achiever;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AchieversTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('portrait')->collection('portrait')->conversion('card')->label('')->circular()->height(40),
                TextColumn::make('name')->label('الاسم')->searchable(query: TranslatableColumn::search('name'))->sortable(query: TranslatableColumn::sort('name')),
                TextColumn::make('field.name')->label('المجال')->badge(),
                TextColumn::make('gender')->label('الجنس')->formatStateUsing(fn (string $state) => Achiever::GENDERS[$state] ?? $state),
                TextColumn::make('achievements_count')->counts('achievements')->label('الإنجازات'),
                IconColumn::make('is_featured')->label('مميّز')->boolean(),
                TextColumn::make('week_from')->label('الأسبوع من')->date('Y-m-d')->toggleable(isToggledHiddenByDefault: true),
                ContentTable::publishedColumn(),
                TextColumn::make('updated_at')->label('آخر تعديل')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('field_id')->label('المجال')->relationship('field', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                SelectFilter::make('gender')->label('الجنس')->options(Achiever::GENDERS),
                ...ContentTable::filters(),
            ])
            ->recordActions([EditAction::make(), ContentTable::openOnSite('achievers.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
