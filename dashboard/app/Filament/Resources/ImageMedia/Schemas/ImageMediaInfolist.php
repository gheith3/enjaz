<?php

namespace App\Filament\Resources\ImageMedia\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ImageMediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('image_media.infolist.sections.content_ar'))
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

                Section::make(__('image_media.infolist.sections.content_en'))
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

                Section::make(__('image_media.infolist.sections.album'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('images')
                            ->label(__('image_media.infolist.fields.images'))
                            ->stacked()
                            ->columnSpanFull(),

                        TextEntry::make('publicFigure.name_ar')
                            ->label(__('image_media.infolist.fields.public_figure'))
                            ->icon('heroicon-o-user')
                            ->placeholder(__('common.placeholders.unassigned')),

                        IconEntry::make('is_active')
                            ->label(__('image_media.infolist.fields.is_active'))
                            ->boolean(),
                    ]),

                Section::make(__('image_media.infolist.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('image_media.infolist.fields.is_published'))
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
