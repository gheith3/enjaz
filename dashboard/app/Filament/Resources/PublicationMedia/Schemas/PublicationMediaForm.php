<?php

namespace App\Filament\Resources\PublicationMedia\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PublicationMediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('publication_media.form.sections.content_ar'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('publication_media.form.placeholders.title_ar')),

                        Textarea::make('description_ar')
                            ->label(__('common.translations.description_ar'))
                            ->rows(3)
                            ->placeholder(__('publication_media.form.placeholders.description_ar')),
                    ]),

                Section::make(__('publication_media.form.sections.content_en'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->maxLength(255)
                            ->placeholder(__('publication_media.form.placeholders.title_en')),

                        Textarea::make('description_en')
                            ->label(__('common.translations.description_en'))
                            ->rows(3)
                            ->placeholder(__('publication_media.form.placeholders.description_en')),
                    ]),

                Section::make(__('publication_media.form.sections.publication'))
                    ->description(__('publication_media.form.sections.publication_description'))
                    ->icon('heroicon-o-book-open')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('file_path')
                            ->label(__('publication_media.form.fields.file'))
                            ->directory('media/publications')
                            ->acceptedFileTypes(['application/pdf'])
                            ->visibility('public')
                            ->required()
                            ->helperText(__('publication_media.form.helpers.file')),

                        FileUpload::make('cover_image_path')
                            ->label(__('publication_media.form.fields.cover_image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::MediaCover->value)
                            ->visibility('public'),

                        Toggle::make('is_active')
                            ->label(__('publication_media.form.fields.is_active'))
                            ->default(true),
                    ]),

                Section::make(__('publication_media.form.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText(__('common.helpers.slug_auto')),

                        Toggle::make('is_published')
                            ->label(__('publication_media.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
