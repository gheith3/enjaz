<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('organizations.form.sections.identity'))
                    ->icon('heroicon-o-identification')
                    ->columns(3)
                    ->schema([
                        Select::make('organization_type_id')
                            ->label(__('organizations.form.fields.organization_type'))
                            ->relationship('organizationType', 'title_ar')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name_ar')
                            ->label(__('organizations.form.fields.name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug((string) $state)))
                            ->placeholder(__('organizations.form.placeholders.name_ar')),

                        TextInput::make('name_en')
                            ->label(__('organizations.form.fields.name_en'))
                            ->maxLength(255)
                            ->placeholder(__('organizations.form.placeholders.name_en')),
                    ]),

                Section::make(__('organizations.form.sections.about'))
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Textarea::make('description_ar')
                            ->label(__('organizations.form.fields.description_ar'))
                            ->rows(4)
                            ->columnSpan(1),

                        Textarea::make('description_en')
                            ->label(__('organizations.form.fields.description_en'))
                            ->rows(4)
                            ->columnSpan(1),
                    ]),

                Section::make(__('organizations.form.sections.branding'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label(__('organizations.form.fields.logo'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::OrganizationLogo->value)
                            ->visibility('public')
                            ->helperText(__('organizations.form.helpers.logo')),

                        FileUpload::make('logo_white_path')
                            ->label(__('organizations.form.fields.logo_white'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::OrganizationLogoWhite->value)
                            ->visibility('public')
                            ->helperText(__('organizations.form.helpers.logo_white')),
                    ]),

                Section::make(__('organizations.form.sections.manager'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('manager_name_ar')
                            ->label(__('organizations.form.fields.manager_name_ar'))
                            ->maxLength(255),

                        TextInput::make('manager_name_en')
                            ->label(__('organizations.form.fields.manager_name_en'))
                            ->maxLength(255),

                        Textarea::make('manager_message_ar')
                            ->label(__('organizations.form.fields.manager_message_ar'))
                            ->rows(4),

                        Textarea::make('manager_message_en')
                            ->label(__('organizations.form.fields.manager_message_en'))
                            ->rows(4),

                        FileUpload::make('manager_image_path')
                            ->label(__('organizations.form.fields.manager_image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::OrganizationManagerImage->value)
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('organizations.form.sections.links'))
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        TextInput::make('website_url')
                            ->label(__('organizations.form.fields.website_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder(__('organizations.form.placeholders.website_url')),
                    ]),

                Section::make(__('organizations.form.sections.publishing'))
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
                            ->label(__('organizations.form.fields.is_published'))
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
