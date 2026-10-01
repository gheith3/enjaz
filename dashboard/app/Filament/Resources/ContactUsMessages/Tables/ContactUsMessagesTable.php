<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactUsMessages\Tables;

use App\Enums\ContactMessageStatus;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactUsMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('contact.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email)
                    ->weight('bold'),

                TextColumn::make('message')
                    ->label(__('contact.table.columns.message'))
                    ->limit(50)
                    ->searchable(),

                SelectColumn::make('status')
                    ->label(__('contact.table.columns.status'))
                    ->options(ContactMessageStatus::class)
                    ->native(false)
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label(__('contact.table.columns.ip_address'))
                    ->fontFamily('mono')
                    ->placeholder('-')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('contact.table.columns.created_at'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('contact.filters.status'))
                    ->options(ContactMessageStatus::class)
                    ->multiple()
                    ->preload(),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('contact.filters.from'))
                            ->native(false),
                        DatePicker::make('until')
                            ->label(__('contact.filters.until'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators['from'] = __('contact.filters.from_indicator', ['date' => Carbon::parse($data['from'])->format('M d, Y')]);
                        }

                        if ($data['until'] ?? null) {
                            $indicators['until'] = __('contact.filters.until_indicator', ['date' => Carbon::parse($data['until'])->format('M d, Y')]);
                        }

                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                ViewAction::make()
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
