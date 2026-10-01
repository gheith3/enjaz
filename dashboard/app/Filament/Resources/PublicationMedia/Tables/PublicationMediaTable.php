<?php

namespace App\Filament\Resources\PublicationMedia\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PublicationMediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label(__('publication_media.table.columns.cover'))
                    ->square(),

                TextColumn::make('title_ar')
                    ->label(__('common.translations.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->title_en)
                    ->weight('bold'),

                TextColumn::make('file_path')
                    ->label(__('publication_media.table.columns.file'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->limit(30)
                    ->url(fn ($record) => $record->file_path ? Storage::url($record->file_path) : null, shouldOpenInNewTab: true),

                IconColumn::make('is_active')
                    ->label(__('publication_media.table.columns.is_active'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('publication_media.table.columns.is_published'))
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
                TernaryFilter::make('is_active')
                    ->label(__('publication_media.filters.is_active'))
                    ->placeholder(__('publication_media.filters.all_publications'))
                    ->trueLabel(__('publication_media.filters.active_only'))
                    ->falseLabel(__('publication_media.filters.inactive_only')),

                TernaryFilter::make('is_published')
                    ->label(__('publication_media.filters.is_published'))
                    ->placeholder(__('publication_media.filters.all_publications'))
                    ->trueLabel(__('publication_media.filters.published_only'))
                    ->falseLabel(__('publication_media.filters.unpublished_only')),
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
