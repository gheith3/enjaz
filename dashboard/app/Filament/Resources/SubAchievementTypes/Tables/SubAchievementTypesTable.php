<?php

namespace App\Filament\Resources\SubAchievementTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubAchievementTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->label(__('common.translations.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title_en')
                    ->label(__('common.translations.title_en'))
                    ->searchable()
                    ->placeholder(__('common.placeholders.not_provided')),

                TextColumn::make('achievementMainType.title_ar')
                    ->label(__('sub_achievement_types.table.columns.main_type'))
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('achievements_count')
                    ->label(__('sub_achievement_types.table.columns.achievements_count'))
                    ->counts('achievements')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('sub_achievement_types.table.columns.is_published'))
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
            ->filters([
                SelectFilter::make('achievement_main_type_id')
                    ->label(__('sub_achievement_types.filters.main_type'))
                    ->relationship('achievementMainType', 'title_ar')
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('is_published')
                    ->label(__('sub_achievement_types.filters.is_published'))
                    ->placeholder(__('sub_achievement_types.filters.all'))
                    ->trueLabel(__('sub_achievement_types.filters.published_only'))
                    ->falseLabel(__('sub_achievement_types.filters.unpublished_only')),
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
