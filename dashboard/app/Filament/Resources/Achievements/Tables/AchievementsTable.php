<?php

namespace App\Filament\Resources\Achievements\Tables;

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

class AchievementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label(__('achievements.table.columns.cover'))
                    ->square(),

                TextColumn::make('title_ar')
                    ->label(__('common.translations.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->achievementMainType?->t('title')),

                TextColumn::make('achievementScope.title_ar')
                    ->label(__('achievements.table.columns.scope'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('achieved_at')
                    ->label(__('achievements.table.columns.achieved_at'))
                    ->date('M d, Y')
                    ->sortable()
                    ->placeholder(__('common.placeholders.not_provided')),

                IconColumn::make('is_published')
                    ->label(__('achievements.table.columns.is_published'))
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
                SelectFilter::make('achievement_scope_id')
                    ->label(__('achievements.filters.scope'))
                    ->relationship('achievementScope', 'title_ar')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('achievement_main_type_id')
                    ->label(__('achievements.filters.main_type'))
                    ->relationship('achievementMainType', 'title_ar')
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('is_published')
                    ->label(__('achievements.filters.is_published'))
                    ->placeholder(__('achievements.filters.all'))
                    ->trueLabel(__('achievements.filters.published_only'))
                    ->falseLabel(__('achievements.filters.unpublished_only')),
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
