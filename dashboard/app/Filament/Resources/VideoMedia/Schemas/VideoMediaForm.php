<?php

namespace App\Filament\Resources\VideoMedia\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoMediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('video_media.form.sections.content_ar'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('video_media.form.placeholders.title_ar')),

                        Textarea::make('description_ar')
                            ->label(__('common.translations.description_ar'))
                            ->rows(3)
                            ->placeholder(__('video_media.form.placeholders.description_ar')),
                    ]),

                Section::make(__('video_media.form.sections.content_en'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->maxLength(255)
                            ->placeholder(__('video_media.form.placeholders.title_en')),

                        Textarea::make('description_en')
                            ->label(__('common.translations.description_en'))
                            ->rows(3)
                            ->placeholder(__('video_media.form.placeholders.description_en')),
                    ]),

                Section::make(__('video_media.form.sections.video'))
                    ->description(__('video_media.form.sections.video_description'))
                    ->icon('heroicon-o-video-camera')
                    ->columns(2)
                    ->schema([
                        TextInput::make('url')
                            ->label(__('video_media.form.fields.url'))
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('video_media.form.placeholders.url'))
                            ->columnSpanFull(),

                        FileUpload::make('cover_image_path')
                            ->label(__('video_media.form.fields.cover_image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::MediaCover->value)
                            ->visibility('public'),

                        Select::make('public_figure_id')
                            ->label(__('video_media.form.fields.public_figure'))
                            ->relationship('publicFigure', 'name_ar')
                            ->native(false)
                            ->searchable()
                            ->preload(),

                        Toggle::make('is_active')
                            ->label(__('video_media.form.fields.is_active'))
                            ->default(true),
                    ]),

                Section::make(__('video_media.form.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText(__('common.helpers.slug_auto')),

                        Toggle::make('is_published')
                            ->label(__('video_media.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
