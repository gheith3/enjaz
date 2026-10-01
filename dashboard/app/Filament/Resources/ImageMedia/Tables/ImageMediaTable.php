<?php

namespace App\Filament\Resources\ImageMedia\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ImageMediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->label(__('common.translations.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->title_en)
                    ->weight('bold'),

                TextColumn::make('images_count')
                    ->label(__('image_media.table.columns.images_count'))
                    ->getStateUsing(fn ($record) => is_array($record->images) ? count($record->images) : 0)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('publicFigure.name_ar')
                    ->label(__('image_media.table.columns.public_figure'))
                    ->placeholder('-')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('image_media.table.columns.is_active'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('image_media.table.columns.is_published'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('common.fields.sort_order'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('public_figure_id')
                    ->label(__('image_media.filters.public_figure'))
                    ->relationship('publicFigure', 'name_ar')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label(__('image_media.filters.is_active'))
                    ->placeholder(__('image_media.filters.all_albums'))
                    ->trueLabel(__('image_media.filters.active_only'))
                    ->falseLabel(__('image_media.filters.inactive_only')),

                TernaryFilter::make('is_published')
                    ->label(__('image_media.filters.is_published'))
                    ->placeholder(__('image_media.filters.all_albums'))
                    ->trueLabel(__('image_media.filters.published_only'))
                    ->falseLabel(__('image_media.filters.unpublished_only')),
            ], layout: FiltersLayout::AboveContentCollapsible)
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
