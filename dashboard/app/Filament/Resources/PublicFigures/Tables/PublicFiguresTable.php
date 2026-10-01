<?php

declare(strict_types=1);

namespace App\Filament\Resources\PublicFigures\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PublicFiguresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('tile_image_path')
                    ->label('')
                    ->circular(),

                TextColumn::make('name_ar')
                    ->label(__('common.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->headline_ar)
                    ->weight('bold'),

                TextColumn::make('achievementMainType.title_ar')
                    ->label(__('public_figures.table.columns.main_type'))
                    ->badge()
                    ->placeholder(__('common.placeholders.unassigned'))
                    ->sortable(),

                TextColumn::make('achievements_count')
                    ->label(__('public_figures.table.columns.achievements_count'))
                    ->counts('achievements')
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_featured')
                    ->label(__('public_figures.table.columns.is_featured'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('in_home_carousel')
                    ->label(__('public_figures.table.columns.in_home_carousel'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('public_figures.table.columns.is_published'))
                    ->boolean()
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
                SelectFilter::make('achievement_main_type_id')
                    ->label(__('public_figures.filters.main_type'))
                    ->relationship('achievementMainType', 'title_ar')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_featured')
                    ->label(__('public_figures.filters.is_featured'))
                    ->native(false),

                TernaryFilter::make('is_published')
                    ->label(__('public_figures.filters.is_published'))
                    ->native(false),
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
