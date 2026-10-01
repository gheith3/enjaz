<?php

namespace App\Filament\Resources\AchievementScopes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AchievementScopeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('achievement_scopes.infolist.sections.titles'))
                    ->icon('heroicon-o-language')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title_ar')
                            ->label(__('common.translations.title_ar'))
                            ->weight('bold'),

                        TextEntry::make('title_en')
                            ->label(__('common.translations.title_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('achievement_scopes.infolist.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('achievement_scopes.infolist.fields.is_published'))
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
