<?php

namespace App\Filament\Resources\AchievementMainTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AchievementMainTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon_path')
                    ->label(__('achievement_main_types.table.columns.icon'))
                    ->square(),

                TextColumn::make('title_ar')
                    ->label(__('common.translations.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->title_en),

                TextColumn::make('sub_achievement_types_count')
                    ->label(__('achievement_main_types.table.columns.sub_types_count'))
                    ->counts('subAchievementTypes')
                    ->badge()
                    ->sortable(),

                IconColumn::make('show_on_home')
                    ->label(__('achievement_main_types.table.columns.show_on_home'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('achievement_main_types.table.columns.is_published'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('common.fields.sort_order'))
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
                    ->label(__('achievement_main_types.filters.is_published'))
                    ->placeholder(__('achievement_main_types.filters.all'))
                    ->trueLabel(__('achievement_main_types.filters.published_only'))
                    ->falseLabel(__('achievement_main_types.filters.unpublished_only')),

                TernaryFilter::make('show_on_home')
                    ->label(__('achievement_main_types.filters.show_on_home'))
                    ->placeholder(__('achievement_main_types.filters.all'))
                    ->trueLabel(__('achievement_main_types.filters.shown_on_home'))
                    ->falseLabel(__('achievement_main_types.filters.hidden_from_home')),
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
