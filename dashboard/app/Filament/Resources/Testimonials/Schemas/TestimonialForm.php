<?php

declare(strict_types=1);

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('testimonials.form.sections.person'))
                    ->description(__('testimonials.form.sections.person_description'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('person_name_ar')
                            ->label(__('testimonials.form.fields.person_name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('testimonials.form.placeholders.person_name_ar')),

                        TextInput::make('person_name_en')
                            ->label(__('testimonials.form.fields.person_name_en'))
                            ->maxLength(255)
                            ->placeholder(__('testimonials.form.placeholders.person_name_en')),

                        TextInput::make('position_ar')
                            ->label(__('testimonials.form.fields.position_ar'))
                            ->maxLength(255)
                            ->placeholder(__('testimonials.form.placeholders.position_ar')),

                        TextInput::make('position_en')
                            ->label(__('testimonials.form.fields.position_en'))
                            ->maxLength(255)
                            ->placeholder(__('testimonials.form.placeholders.position_en')),
                    ]),

                Section::make(__('testimonials.form.sections.quote'))
                    ->description(__('testimonials.form.sections.quote_description'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->columns(2)
                    ->schema([
                        Textarea::make('quote_ar')
                            ->label(__('testimonials.form.fields.quote_ar'))
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('quote_en')
                            ->label(__('testimonials.form.fields.quote_en'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('testimonials.form.sections.photo'))
                    ->description(__('testimonials.form.sections.photo_description'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('photo_path')
                            ->label(__('testimonials.form.fields.photo'))
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['252:252'])
                            ->directory(FileStoragePath::TestimonialPhoto->value)
                            ->visibility('public'),
                    ]),

                Section::make(__('testimonials.form.sections.publishing'))
                    ->description(__('testimonials.form.sections.publishing_description'))
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
                            ->label(__('testimonials.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
