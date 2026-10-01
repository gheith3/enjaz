<?php

namespace App\Filament\Resources\News\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('news.form.sections.content_ar'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('news.form.placeholders.title_ar')),

                        Textarea::make('excerpt_ar')
                            ->label(__('news.form.fields.excerpt_ar'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder(__('news.form.placeholders.excerpt_ar')),

                        Textarea::make('body_ar')
                            ->label(__('news.form.fields.body_ar'))
                            ->rows(10)
                            ->placeholder(__('news.form.placeholders.body_ar'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('news.form.sections.content_en'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->maxLength(255)
                            ->placeholder(__('news.form.placeholders.title_en')),

                        Textarea::make('excerpt_en')
                            ->label(__('news.form.fields.excerpt_en'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder(__('news.form.placeholders.excerpt_en')),

                        Textarea::make('body_en')
                            ->label(__('news.form.fields.body_en'))
                            ->rows(10)
                            ->placeholder(__('news.form.placeholders.body_en'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('news.form.sections.details'))
                    ->description(__('news.form.sections.details_description'))
                    ->icon('heroicon-o-newspaper')
                    ->columns(3)
                    ->schema([
                        FileUpload::make('cover_image_path')
                            ->label(__('news.form.fields.cover_image'))
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['345:208'])
                            ->directory(FileStoragePath::NewsCover->value)
                            ->visibility('public')
                            ->helperText(__('news.form.helpers.cover_image'))
                            ->columnSpanFull(),

                        DateTimePicker::make('published_at')
                            ->label(__('news.form.fields.published_at'))
                            ->native(false),

                        Select::make('organization_id')
                            ->label(__('news.form.fields.organization'))
                            ->relationship('organization', 'name_ar')
                            ->native(false)
                            ->searchable()
                            ->preload(),

                        Toggle::make('show_on_about')
                            ->label(__('news.form.fields.show_on_about'))
                            ->helperText(__('news.form.helpers.show_on_about')),
                    ]),

                Section::make(__('news.form.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText(__('common.helpers.slug_auto')),

                        Toggle::make('is_published')
                            ->label(__('news.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
