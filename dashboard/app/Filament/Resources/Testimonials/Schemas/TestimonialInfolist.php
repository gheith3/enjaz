<?php

declare(strict_types=1);

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('testimonials.infolist.sections.person'))
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('photo_path')
                            ->label('')
                            ->circular()
                            ->size(120),

                        TextEntry::make('person_name_ar')
                            ->label(__('testimonials.infolist.fields.person_name_ar'))
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('person_name_en')
                            ->label(__('testimonials.infolist.fields.person_name_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('position_ar')
                            ->label(__('testimonials.infolist.fields.position_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('position_en')
                            ->label(__('testimonials.infolist.fields.position_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('testimonials.infolist.sections.quote'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->schema([
                        TextEntry::make('quote_ar')
                            ->label(__('testimonials.infolist.fields.quote_ar')),

                        TextEntry::make('quote_en')
                            ->label(__('testimonials.infolist.fields.quote_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('testimonials.infolist.sections.publishing'))
                    ->icon('heroicon-o-clock')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),

                        IconEntry::make('is_published')
                            ->label(__('testimonials.infolist.fields.is_published'))
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
