<?php

namespace App\Filament\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label(__('organizations.table.columns.logo'))
                    ->square(),

                TextColumn::make('name_ar')
                    ->label(__('organizations.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->organizationType?->title_ar)
                    ->weight('bold'),

                TextColumn::make('website_url')
                    ->label(__('organizations.table.columns.website_url'))
                    ->icon('heroicon-o-globe-alt')
                    ->iconColor('gray')
                    ->placeholder('-')
                    ->toggleable(),

                IconColumn::make('is_published')
                    ->label(__('organizations.table.columns.is_published'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('organizations.table.columns.sort_order'))
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
            ->filters([
                SelectFilter::make('organization_type_id')
                    ->label(__('organizations.filters.type'))
                    ->relationship('organizationType', 'title_ar')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_published')
                    ->label(__('organizations.filters.is_published'))
                    ->placeholder(__('organizations.filters.all'))
                    ->trueLabel(__('organizations.filters.published_only'))
                    ->falseLabel(__('organizations.filters.unpublished_only')),
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
