<?php

namespace App\Filament\Resources\Achievements\Schemas;

use App\Enums\FileStoragePath;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use App\Models\SubAchievementType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('achievements.form.sections.classification'))
                    ->icon('heroicon-o-squares-2x2')
                    ->columns(3)
                    ->schema([
                        Select::make('achievement_scope_id')
                            ->label(__('achievements.form.fields.scope'))
                            ->options(AchievementScope::query()->pluck('title_ar', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('achievement_main_type_id')
                            ->label(__('achievements.form.fields.main_type'))
                            ->options(AchievementMainType::query()->pluck('title_ar', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('sub_achievement_type_id', null)),

                        Select::make('sub_achievement_type_id')
                            ->label(__('achievements.form.fields.sub_type'))
                            ->options(fn (Get $get) => SubAchievementType::query()
                                ->when(
                                    $get('achievement_main_type_id'),
                                    fn ($query, $mainTypeId) => $query->where('achievement_main_type_id', $mainTypeId)
                                )
                                ->pluck('title_ar', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText(__('achievements.form.helpers.sub_type')),
                    ]),

                Section::make(__('achievements.form.sections.content_ar'))
                    ->icon('heroicon-o-language')
                    ->schema([
                        TextInput::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set, Get $get) {
                                if ($operation !== 'create' || filled($get('slug'))) {
                                    return;
                                }

                                $set('slug', Str::slug((string) $state) ?: null);
                            }),

                        Textarea::make('excerpt_ar')
                            ->label(__('achievements.form.fields.excerpt_ar'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('description_ar')
                            ->label(__('common.translations.description_ar'))
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('achievements.form.sections.content_en'))
                    ->icon('heroicon-o-language')
                    ->schema([
                        TextInput::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->maxLength(255),

                        Textarea::make('excerpt_en')
                            ->label(__('achievements.form.fields.excerpt_en'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('description_en')
                            ->label(__('common.translations.description_en'))
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('achievements.form.sections.media'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image_path')
                            ->label(__('achievements.form.fields.cover_image'))
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['473:283'])
                            ->directory(FileStoragePath::AchievementCover->value)
                            ->visibility('public'),

                        TextInput::make('video_url')
                            ->label(__('achievements.form.fields.video_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder(__('achievements.form.placeholders.video_url')),
                    ]),

                Section::make(__('achievements.form.sections.details'))
                    ->icon('heroicon-o-calendar-days')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('achieved_at')
                            ->label(__('achievements.form.fields.achieved_at'))
                            ->native(false),

                        TextInput::make('country_ar')
                            ->label(__('achievements.form.fields.country_ar'))
                            ->maxLength(255),

                        TextInput::make('country_en')
                            ->label(__('achievements.form.fields.country_en'))
                            ->maxLength(255),
                    ]),

                Section::make(__('achievements.form.sections.relations'))
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        Select::make('public_figures')
                            ->label(__('achievements.form.fields.public_figures'))
                            ->multiple()
                            ->relationship('publicFigures', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('organizations')
                            ->label(__('achievements.form.fields.organizations'))
                            ->multiple()
                            ->relationship('organizations', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ]),

                Section::make(__('achievements.form.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('achievements.form.helpers.slug')),

                        Toggle::make('is_published')
                            ->label(__('achievements.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
