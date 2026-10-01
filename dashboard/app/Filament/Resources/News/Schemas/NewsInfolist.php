<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('news.infolist.sections.content_ar'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->weight('bold'),

                        TextEntry::make('excerpt_ar')
                            ->label(__('news.infolist.fields.excerpt_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('body_ar')
                            ->label(__('news.infolist.fields.body_ar'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('news.infolist.sections.content_en'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('excerpt_en')
                            ->label(__('news.infolist.fields.excerpt_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('body_en')
                            ->label(__('news.infolist.fields.body_en'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('news.infolist.sections.details'))
                    ->icon('heroicon-o-newspaper')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('cover_image_path')
                            ->label(__('news.infolist.fields.cover_image'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),

                        TextEntry::make('published_at')
                            ->label(__('news.infolist.fields.published_at'))
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-o-calendar')
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('organization.name_ar')
                            ->label(__('news.infolist.fields.organization'))
                            ->icon('heroicon-o-building-office')
                            ->placeholder(__('common.placeholders.unassigned')),

                        IconEntry::make('show_on_about')
                            ->label(__('news.infolist.fields.show_on_about'))
                            ->boolean(),
                    ]),

                Section::make(__('news.infolist.sections.publishing'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('news.infolist.fields.is_published'))
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
