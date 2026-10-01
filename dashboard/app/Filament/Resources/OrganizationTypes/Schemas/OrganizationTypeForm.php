<?php

namespace App\Filament\Resources\OrganizationTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrganizationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('organization_types.form.sections.identity'))
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_ar')
                            ->label(__('organization_types.form.fields.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug((string) $state)))
                            ->placeholder(__('organization_types.form.placeholders.title_ar')),

                        TextInput::make('title_en')
                            ->label(__('organization_types.form.fields.title_en'))
                            ->maxLength(255)
                            ->placeholder(__('organization_types.form.placeholders.title_en')),
                    ]),

                Section::make(__('organization_types.form.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText(__('common.helpers.slug_auto')),

                        Toggle::make('is_published')
                            ->label(__('organization_types.form.fields.is_published'))
                            ->default(false),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),
            ]);
    }
}
