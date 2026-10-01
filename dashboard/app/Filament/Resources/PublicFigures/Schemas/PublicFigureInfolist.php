<?php

declare(strict_types=1);

namespace App\Filament\Resources\PublicFigures\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PublicFigureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('public_figures.infolist.sections.identity'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name_ar')
                            ->label(__('common.translations.name_ar'))
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('name_en')
                            ->label(__('common.translations.name_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('headline_ar')
                            ->label(__('public_figures.infolist.fields.headline_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('headline_en')
                            ->label(__('public_figures.infolist.fields.headline_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('achievementMainType.title_ar')
                            ->label(__('public_figures.infolist.fields.main_type'))
                            ->badge()
                            ->placeholder(__('common.placeholders.unassigned')),

                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),
                    ]),

                Section::make(__('public_figures.infolist.sections.biography'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('description_ar')
                            ->label(__('public_figures.infolist.fields.description_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('description_en')
                            ->label(__('public_figures.infolist.fields.description_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('story_ar')
                            ->label(__('public_figures.infolist.fields.story_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('story_en')
                            ->label(__('public_figures.infolist.fields.story_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('public_figures.infolist.sections.images'))
                    ->icon('heroicon-o-photo')
                    ->columns(4)
                    ->schema([
                        ImageEntry::make('image_path')
                            ->label(__('public_figures.infolist.fields.image')),

                        ImageEntry::make('tile_image_path')
                            ->label(__('public_figures.infolist.fields.tile_image')),

                        ImageEntry::make('cutout_image_path')
                            ->label(__('public_figures.infolist.fields.cutout_image')),

                        ImageEntry::make('banner_image_path')
                            ->label(__('public_figures.infolist.fields.banner_image')),
                    ]),

                Section::make(__('public_figures.infolist.sections.flags'))
                    ->icon('heroicon-o-flag')
                    ->columns(3)
                    ->schema([
                        IconEntry::make('is_featured')
                            ->label(__('public_figures.infolist.fields.is_featured'))
                            ->boolean(),

                        IconEntry::make('in_home_carousel')
                            ->label(__('public_figures.infolist.fields.in_home_carousel'))
                            ->boolean(),

                        IconEntry::make('is_published')
                            ->label(__('public_figures.infolist.fields.is_published'))
                            ->boolean(),
                    ]),

                Section::make(__('public_figures.infolist.sections.social_contact'))
                    ->icon('heroicon-o-share')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('linkedin_url')
                            ->label(__('public_figures.infolist.fields.linkedin_url'))
                            ->url(fn ($record) => $record->linkedin_url)
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        TextEntry::make('twitter_url')
                            ->label(__('public_figures.infolist.fields.twitter_url'))
                            ->url(fn ($record) => $record->twitter_url)
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        TextEntry::make('instagram_url')
                            ->label(__('public_figures.infolist.fields.instagram_url'))
                            ->url(fn ($record) => $record->instagram_url)
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        TextEntry::make('facebook_url')
                            ->label(__('public_figures.infolist.fields.facebook_url'))
                            ->url(fn ($record) => $record->facebook_url)
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        TextEntry::make('whatsapp_number')
                            ->label(__('public_figures.infolist.fields.whatsapp_number'))
                            ->icon('heroicon-o-phone')
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        TextEntry::make('email')
                            ->label(__('common.fields.email'))
                            ->icon('heroicon-o-envelope')
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label(__('common.fields.phone'))
                            ->icon('heroicon-o-phone')
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),
                    ]),

                Section::make(__('public_figures.infolist.sections.publishing'))
                    ->icon('heroicon-o-clock')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sort_order')
                            ->label(__('common.fields.sort_order')),

                        TextEntry::make('created_at')
                            ->label(__('common.fields.created_at'))
                            ->dateTime('M d, Y'),

                        TextEntry::make('updated_at')
                            ->label(__('common.fields.updated_at'))
                            ->dateTime('M d, Y'),
                    ]),
            ]);
    }
}
