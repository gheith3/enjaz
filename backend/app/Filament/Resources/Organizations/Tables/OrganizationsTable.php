<?php

namespace App\Filament\Resources\Organizations\Tables;

use App\Filament\Support\ContentTable;
use App\Filament\Support\TranslatableColumn;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')->collection('logo')->label('')->height(36),
                TextColumn::make('name')->label('المؤسسة')->searchable(query: TranslatableColumn::search('name'))->sortable(query: TranslatableColumn::sort('name')),
                TextColumn::make('type.name')->label('النوع')->badge(),
                TextColumn::make('achievers_count')->counts('achievers')->label('المنجزون')->toggleable(),
                TextColumn::make('achievements_count')->counts('achievements')->label('الإنجازات')->toggleable(),
                IconColumn::make('is_partner')->label('شريك')->boolean()->toggleable(),
                ContentTable::publishedColumn(),
            ])
            ->filters([
                SelectFilter::make('organization_type_id')->label('النوع')->relationship('type', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                TernaryFilter::make('is_partner')->label('شركاء النجاح'),
                ...ContentTable::filters(),
            ])
            ->recordActions([EditAction::make(), ContentTable::openOnSite('organizations.show'), RestoreAction::make()])
            ->toolbarActions([ContentTable::bulkActions()]);
    }
}
