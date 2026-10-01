<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_path')
                    ->label(__('news.table.columns.cover'))
                    ->square(),

                TextColumn::make('title_ar')
                    ->label(__('common.translations.title_ar'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->title_en)
                    ->weight('bold'),

                TextColumn::make('organization.name_ar')
                    ->label(__('news.table.columns.organization'))
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label(__('news.table.columns.published_at'))
                    ->dateTime('M d, Y')
                    ->placeholder('-')
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('news.table.columns.is_published'))
                    ->boolean()
                    ->sortable(),

                IconColumn::make('show_on_about')
                    ->label(__('news.table.columns.show_on_about'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('organization_id')
                    ->label(__('news.filters.organization'))
                    ->relationship('organization', 'name_ar')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_published')
                    ->label(__('news.filters.is_published'))
                    ->placeholder(__('news.filters.all_news'))
                    ->trueLabel(__('news.filters.published_only'))
                    ->falseLabel(__('news.filters.unpublished_only')),

                Filter::make('published_at')
                    ->label(__('news.filters.published_range'))
                    ->schema([
                        DatePicker::make('published_from')
                            ->label(__('news.filters.published_from'))
                            ->native(false),
                        DatePicker::make('published_until')
                            ->label(__('news.filters.published_until'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
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
