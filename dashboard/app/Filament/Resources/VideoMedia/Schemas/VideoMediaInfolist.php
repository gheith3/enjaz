<?php

namespace App\Filament\Resources\VideoMedia\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VideoMediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('video_media.infolist.sections.content_ar'))
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

                Section::make(__('video_media.infolist.sections.content_en'))
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

                Section::make(__('video_media.infolist.sections.video'))
                    ->icon('heroicon-o-video-camera')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('url')
                            ->label(__('video_media.infolist.fields.url'))
                            ->icon('heroicon-o-link')
                            ->url(fn ($record) => $record->url, shouldOpenInNewTab: true)
                            ->columnSpanFull(),

                        ImageEntry::make('cover_image_path')
                            ->label(__('video_media.infolist.fields.cover_image'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('publicFigure.name_ar')
                            ->label(__('video_media.infolist.fields.public_figure'))
                            ->icon('heroicon-o-user')
                            ->placeholder(__('common.placeholders.unassigned')),

                        IconEntry::make('is_active')
                            ->label(__('video_media.infolist.fields.is_active'))
                            ->boolean(),
                    ]),

                Section::make(__('video_media.infolist.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('video_media.infolist.fields.is_published'))
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
