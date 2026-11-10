<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

/**
 * Read-only history of dashboard changes (spatie/laravel-activitylog).
 */
class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'النظام';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'تغيير';

    protected static ?string $pluralModelLabel = 'سجل التغييرات';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('الوقت')->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('causer.name')->label('المستخدم')->placeholder('النظام'),
                TextColumn::make('event')->label('العملية')->badge()
                    ->formatStateUsing(fn (?string $state) => ['created' => 'إضافة', 'updated' => 'تعديل', 'deleted' => 'حذف'][$state] ?? $state),
                TextColumn::make('subject_type')->label('النوع')->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : null),
                TextColumn::make('subject_id')->label('رقم السجل'),
                TextColumn::make('properties')->label('الحقول')->limit(80)
                    ->state(fn (Activity $record) => collect($record->properties->get('attributes', []))->keys()->implode('، '))
                    ->tooltip(fn (Activity $record) => Str::limit(json_encode($record->properties->get('attributes', []), JSON_UNESCAPED_UNICODE), 500)),
            ])
            ->filters([
                SelectFilter::make('event')->label('العملية')->options(['created' => 'إضافة', 'updated' => 'تعديل', 'deleted' => 'حذف']),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListActivities::route('/')];
    }
}
