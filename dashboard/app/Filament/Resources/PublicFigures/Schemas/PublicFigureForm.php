<?php

declare(strict_types=1);

namespace App\Filament\Resources\PublicFigures\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PublicFigureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('public_figures.form.sections.identity'))
                    ->description(__('public_figures.form.sections.identity_description'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name_ar')
                            ->label(__('common.translations.name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.name_ar')),

                        TextInput::make('name_en')
                            ->label(__('common.translations.name_en'))
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.name_en')),

                        TextInput::make('headline_ar')
                            ->label(__('public_figures.form.fields.headline_ar'))
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.headline_ar')),

                        TextInput::make('headline_en')
                            ->label(__('public_figures.form.fields.headline_en'))
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.headline_en')),
                    ]),

                Section::make(__('public_figures.form.sections.biography'))
                    ->description(__('public_figures.form.sections.biography_description'))
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        Textarea::make('description_ar')
                            ->label(__('public_figures.form.fields.description_ar'))
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('description_en')
                            ->label(__('public_figures.form.fields.description_en'))
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('story_ar')
                            ->label(__('public_figures.form.fields.story_ar'))
                            ->rows(6)
                            ->columnSpanFull(),

                        Textarea::make('story_en')
                            ->label(__('public_figures.form.fields.story_en'))
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('public_figures.form.sections.images'))
                    ->description(__('public_figures.form.sections.images_description'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label(__('public_figures.form.fields.image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::PublicFigureImage->value)
                            ->visibility('public'),

                        FileUpload::make('tile_image_path')
                            ->label(__('public_figures.form.fields.tile_image'))
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['351:427'])
                            ->directory(FileStoragePath::PublicFigureTile->value)
                            ->visibility('public')
                            ->helperText(__('public_figures.form.helpers.tile_image')),

                        FileUpload::make('cutout_image_path')
                            ->label(__('public_figures.form.fields.cutout_image'))
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['848:477'])
                            ->directory(FileStoragePath::PublicFigureCutout->value)
                            ->visibility('public')
                            ->helperText(__('public_figures.form.helpers.cutout_image')),

                        FileUpload::make('banner_image_path')
                            ->label(__('public_figures.form.fields.banner_image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::PublicFigureBanner->value)
                            ->visibility('public'),
                    ]),

                Section::make(__('public_figures.form.sections.classification'))
                    ->description(__('public_figures.form.sections.classification_description'))
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Select::make('achievement_main_type_id')
                            ->label(__('public_figures.form.fields.main_type'))
                            ->relationship('achievementMainType', 'title_ar')
                            ->native(false)
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make(__('public_figures.form.sections.flags'))
                    ->description(__('public_figures.form.sections.flags_description'))
                    ->icon('heroicon-o-flag')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_featured')
                            ->label(__('public_figures.form.fields.is_featured'))
                            ->helperText(__('public_figures.form.helpers.is_featured')),

                        Toggle::make('in_home_carousel')
                            ->label(__('public_figures.form.fields.in_home_carousel'))
                            ->helperText(__('public_figures.form.helpers.in_home_carousel')),
                    ]),

                Section::make(__('public_figures.form.sections.social_contact'))
                    ->description(__('public_figures.form.sections.social_contact_description'))
                    ->icon('heroicon-o-share')
                    ->columns(2)
                    ->schema([
                        TextInput::make('linkedin_url')
                            ->label(__('public_figures.form.fields.linkedin_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.linkedin_url')),

                        TextInput::make('twitter_url')
                            ->label(__('public_figures.form.fields.twitter_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.twitter_url')),

                        TextInput::make('instagram_url')
                            ->label(__('public_figures.form.fields.instagram_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.instagram_url')),

                        TextInput::make('facebook_url')
                            ->label(__('public_figures.form.fields.facebook_url'))
                            ->url()
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.facebook_url')),

                        TextInput::make('whatsapp_number')
                            ->label(__('public_figures.form.fields.whatsapp_number'))
                            ->tel()
                            ->maxLength(20)
                            ->placeholder(__('public_figures.form.placeholders.whatsapp_number')),

                        TextInput::make('email')
                            ->label(__('common.fields.email'))
                            ->email()
                            ->maxLength(255)
                            ->placeholder(__('public_figures.form.placeholders.email')),

                        TextInput::make('phone')
                            ->label(__('common.fields.phone'))
                            ->tel()
                            ->maxLength(20)
                            ->placeholder(__('public_figures.form.placeholders.phone')),
                    ]),

                Section::make(__('public_figures.form.sections.publishing'))
                    ->description(__('public_figures.form.sections.publishing_description'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->unique(ignoreRecord: true)
                            ->nullable()
                            ->maxLength(255)
                            ->helperText(__('common.helpers.slug_auto')),

                        Toggle::make('is_published')
                            ->label(__('public_figures.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
