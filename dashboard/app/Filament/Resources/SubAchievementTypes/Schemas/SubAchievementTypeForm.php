<?php

namespace App\Filament\Resources\SubAchievementTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SubAchievementTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('sub_achievement_types.form.sections.classification'))
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Select::make('achievement_main_type_id')
                            ->label(__('sub_achievement_types.form.fields.main_type'))
                            ->relationship('achievementMainType', 'title_ar')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ]),

                Section::make(__('sub_achievement_types.form.sections.titles'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
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

                        TextInput::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->maxLength(255),
                    ]),

                Section::make(__('sub_achievement_types.form.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('sub_achievement_types.form.helpers.slug')),

                        Toggle::make('is_published')
                            ->label(__('sub_achievement_types.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
