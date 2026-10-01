<?php

namespace App\Filament\Resources\OrganizationTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrganizationTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->label(__('organization_types.table.columns.title'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->title_en)
                    ->weight('bold'),

                TextColumn::make('organizations_count')
                    ->label(__('organization_types.table.columns.organizations_count'))
                    ->counts('organizations')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('organization_types.table.columns.is_published'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('organization_types.table.columns.sort_order'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('common.fields.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('common.fields.updated_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_published')
                    ->label(__('organization_types.filters.is_published'))
                    ->placeholder(__('organization_types.filters.all'))
                    ->trueLabel(__('organization_types.filters.published_only'))
                    ->falseLabel(__('organization_types.filters.unpublished_only')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
                EditAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
