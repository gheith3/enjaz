<?php

namespace App\Filament\Resources\Achievements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AchievementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('achievements.infolist.sections.classification'))
                    ->icon('heroicon-o-squares-2x2')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('achievementScope.title_ar')
                            ->label(__('achievements.infolist.fields.scope'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('achievementMainType.title_ar')
                            ->label(__('achievements.infolist.fields.main_type'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('subAchievementType.title_ar')
                            ->label(__('achievements.infolist.fields.sub_type'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('achievements.infolist.sections.content'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->weight('bold'),

                        TextEntry::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('excerpt_ar')
                            ->label(__('achievements.infolist.fields.excerpt_ar'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),

                        TextEntry::make('excerpt_en')
                            ->label(__('achievements.infolist.fields.excerpt_en'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),

                        TextEntry::make('description_ar')
                            ->label(__('common.translations.description_ar'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),

                        TextEntry::make('description_en')
                            ->label(__('common.translations.description_en'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('achievements.infolist.sections.media'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('cover_image_path')
                            ->label(__('achievements.infolist.fields.cover_image'))
                            ->visibility('public'),

                        TextEntry::make('video_url')
                            ->label(__('achievements.infolist.fields.video_url'))
                            ->url(fn ($record) => $record->video_url)
                            ->openUrlInNewTab()
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('achievements.infolist.sections.details'))
                    ->icon('heroicon-o-calendar-days')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('achieved_at')
                            ->label(__('achievements.infolist.fields.achieved_at'))
                            ->date('M d, Y')
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('country_ar')
                            ->label(__('achievements.infolist.fields.country_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('country_en')
                            ->label(__('achievements.infolist.fields.country_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('achievements.infolist.sections.relations'))
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('publicFigures.name_ar')
                            ->label(__('achievements.infolist.fields.public_figures'))
                            ->badge()
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('organizations.name_ar')
                            ->label(__('achievements.infolist.fields.organizations'))
                            ->badge()
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('achievements.infolist.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('achievements.infolist.fields.is_published'))
                            ->boolean(),

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
