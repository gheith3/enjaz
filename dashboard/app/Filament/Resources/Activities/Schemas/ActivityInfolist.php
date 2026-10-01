<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('activities.infolist.sections.causer_information'))
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('causer_type')
                            ->label(__('activities.infolist.fields.causer_type'))
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : __('activities.infolist.placeholders.system'))
                            ->icon('heroicon-o-user-group')
                            ->placeholder(__('activities.infolist.placeholders.system')),

                        TextEntry::make('causer_id')
                            ->label(__('activities.infolist.fields.causer_id'))
                            ->fontFamily('mono')
                            ->icon('heroicon-o-hashtag')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('causer.name')
                            ->label(__('activities.infolist.fields.causer_name'))
                            ->icon('heroicon-o-user')
                            ->placeholder(__('activities.infolist.placeholders.system'))
                            ->weight('bold'),

                        TextEntry::make('causer.email')
                            ->label(__('activities.infolist.fields.causer_email'))
                            ->icon('heroicon-o-envelope')
                            ->placeholder('-')
                            ->copyable()
                            ->columnSpanFull(),
                    ]),

                Section::make(__('activities.infolist.sections.subject_history_timeline'))
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ViewEntry::make('subject_history')
                            ->view('filament.infolists.components.activity-timeline')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => ! empty($record->subject_type) && ! empty($record->subject_id)),

                Section::make(__('activities.infolist.sections.activity_details'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('activities.infolist.fields.activity_id'))
                            ->icon('heroicon-o-finger-print')
                            ->fontFamily('mono')
                            ->size('sm')
                            ->copyable(),

                        TextEntry::make('log_name')
                            ->label(__('activities.infolist.fields.log_name'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'default' => 'gray',
                                'auth' => 'info',
                                'system' => 'warning',
                                default => 'primary',
                            }),

                        TextEntry::make('event')
                            ->label(__('activities.infolist.fields.event'))
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
                                : '-'),

                        TextEntry::make('subject_type')
                            ->label(__('activities.infolist.fields.subject_type'))
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                            ->icon('heroicon-o-cube')
                            ->placeholder('-'),

                        TextEntry::make('subject_id')
                            ->label(__('activities.infolist.fields.subject_id'))
                            ->fontFamily('mono')
                            ->icon('heroicon-o-hashtag')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('subject_name')
                            ->label(__('activities.infolist.fields.subject_name'))
                            ->getStateUsing(function ($record): ?string {
                                $subject = $record->subject;

                                if (! $subject) {
                                    return null;
                                }

                                return $subject->name->en ?? $subject->title->en ?? $subject->label->en ?? "#{$subject->id}";
                            })
                            ->icon('heroicon-o-identification')
                            ->placeholder('-'),
                    ]),

                Section::make(__('activities.infolist.sections.additional_properties'))
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        TextEntry::make('all_properties')
                            ->label('')
                            ->getStateUsing(fn ($record): ?string => ! empty($record->properties)
                                ? json_encode($record->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                : null)
                            ->fontFamily('mono')
                            ->size('sm')
                            ->placeholder(__('activities.infolist.placeholders.no_additional_properties'))
                            ->copyable(),
                    ])
                    ->visible(fn ($record): bool => ! empty($record->properties) && empty($record->properties['old']) && empty($record->properties['attributes']))
                    ->collapsed(),

                Section::make(__('activities.infolist.sections.batch_information'))
                    ->icon('heroicon-o-rectangle-group')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('batch_uuid')
                            ->label(__('activities.infolist.fields.batch_uuid'))
                            ->fontFamily('mono')
                            ->size('sm')
                            ->icon('heroicon-o-finger-print')
                            ->copyable(),

                        TextEntry::make('batch_count')
                            ->label(__('activities.infolist.fields.activities_in_batch'))
                            ->getStateUsing(function ($record): int {
                                if (! $record->batch_uuid) {
                                    return 0;
                                }

                                return Activity::where('batch_uuid', $record->batch_uuid)->count();
                            })
                            ->icon('heroicon-o-queue-list')
                            ->badge()
                            ->color('info'),
                    ])
                    ->visible(fn ($record): bool => ! empty($record->batch_uuid)),

                Section::make(__('activities.infolist.sections.timestamps'))
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('common.fields.created_at'))
                            ->dateTime('F j, Y \a\t g:i:s A')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('updated_at')
                            ->label(__('common.fields.updated_at'))
                            ->dateTime('F j, Y \a\t g:i:s A')
                            ->icon('heroicon-o-pencil-square'),
                    ]),

            ]);
    }
}
