<?php

namespace App\Filament\Resources\PublicationMedia\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class PublicationMediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('publication_media.infolist.sections.content_ar'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->weight('bold'),

                        TextEntry::make('description_ar')
                            ->label(__('common.translations.description_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('publication_media.infolist.sections.content_en'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('description_en')
                            ->label(__('common.translations.description_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('publication_media.infolist.sections.publication'))
                    ->icon('heroicon-o-book-open')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('file_path')
                            ->label(__('publication_media.infolist.fields.file'))
                            ->icon('heroicon-o-document-arrow-down')
                            ->url(fn ($record) => $record->file_path ? Storage::url($record->file_path) : null, shouldOpenInNewTab: true)
                            ->placeholder(__('common.placeholders.not_provided')),

                        ImageEntry::make('cover_image_path')
                            ->label(__('publication_media.infolist.fields.cover_image'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_active')
                            ->label(__('publication_media.infolist.fields.is_active'))
                            ->boolean(),
                    ]),

                Section::make(__('publication_media.infolist.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('publication_media.infolist.fields.is_published'))
                            ->boolean(),

                        TextEntry::make('sort_order')
                            ->label(__('common.fields.sort_order')),

                        TextEntry::make('created_at')
                            ->label(__('common.fields.created_at'))
                            ->dateTime('M d, Y H:i'),

                        TextEntry::make('updated_at')
                            ->label(__('common.fields.updated_at'))
                            ->dateTime('M d, Y H:i'),
                    ]),
            ]);
    }
}
