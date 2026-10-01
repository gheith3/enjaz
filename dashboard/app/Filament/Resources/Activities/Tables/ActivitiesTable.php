<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities\Tables;

use Carbon\Carbon;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('common.fields.date'))
                    ->dateTime('M d, Y H:i:s')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray'),

                // TextColumn::make('subject_type')
                //     ->label('Type')
                //     ->badge()
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('event')
                    ->label(__('activities.table.columns.event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        'login' => 'success',
                        'logout' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? (Lang::has('filament/activities.events.'.$state)
                            ? __('activities.events.'.$state)
                            : Str::title($state))
                        : '-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject_type')
                    ->label(__('activities.table.columns.subject'))
                    ->formatStateUsing(function (?string $state, $record): string {
                        if (! $state) {
                            return '-';
                        }

                        $modelName = class_basename($state);
                        $subjectId = $record->subject_id;

                        return "{$modelName} #{$subjectId}";
                    })
                    ->icon('heroicon-o-cube')
                    ->iconColor('gray')
                    ->searchable(['subject_type', 'subject_id'])
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label(__('activities.table.columns.caused_by'))
                    ->default(__('activities.infolist.placeholders.system'))
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('batch_uuid')
                    ->label(__('activities.table.columns.batch'))
                    ->limit(8)
                    ->placeholder('-')
                    ->fontFamily('mono')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('activities.filters.log_name'))
                    ->options(fn () => Activity::query()
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->toArray())
                    ->multiple()
                    ->preload(),

                SelectFilter::make('event')
                    ->label(__('activities.filters.event'))
                    ->options([
                        'created' => __('activities.events.created'),
                        'updated' => __('activities.events.updated'),
                        'deleted' => __('activities.events.deleted'),
                        'restored' => __('activities.events.restored'),
                        'login' => __('activities.events.login'),
                        'logout' => __('activities.events.logout'),
                    ])
                    ->multiple()
                    ->preload(),

                SelectFilter::make('subject_type')
                    ->label(__('activities.filters.subject_type'))
                    ->options(fn () => Activity::query()
                        ->whereNotNull('subject_type')
                        ->distinct()
                        ->pluck('subject_type')
                        ->mapWithKeys(fn ($type) => [$type => class_basename($type)])
                        ->toArray())
                    ->multiple()
                    ->preload(),

                SelectFilter::make('causer_id')
                    ->label(__('activities.filters.caused_by'))
                    ->options(function (): array {
                        return Activity::query()
                            ->whereNotNull('causer_id')
                            ->whereNotNull('causer_type')
                            ->with('causer')
                            ->get()
                            ->pluck('causer')
                            ->filter()
                            ->unique('id')
                            ->mapWithKeys(fn ($causer) => [$causer->id => $causer->name ?? $causer->email ?? "#{$causer->id}"])
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('activities.filters.from')),
                        DatePicker::make('until')
                            ->label(__('activities.filters.until')),
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
                            $indicators['from'] = __('activities.filters.from_indicator', ['date' => Carbon::parse($data['from'])->format('M d, Y')]);
                        }

                        if ($data['until'] ?? null) {
                            $indicators['until'] = __('activities.filters.until_indicator', ['date' => Carbon::parse($data['until'])->format('M d, Y')]);
                        }

                        return $indicators;
                    }),

                Filter::make('has_batch')
                    ->label(__('activities.filters.has_batch'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('batch_uuid')),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
