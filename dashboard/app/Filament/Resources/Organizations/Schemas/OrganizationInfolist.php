<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('organizations.infolist.sections.identity'))
                    ->icon('heroicon-o-identification')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name_ar')
                            ->label(__('organizations.infolist.fields.name_ar'))
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('name_en')
                            ->label(__('organizations.infolist.fields.name_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('organizationType.title_ar')
                            ->label(__('organizations.infolist.fields.organization_type'))
                            ->badge(),
                    ]),

                Section::make(__('organizations.infolist.sections.about'))
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('description_ar')
                            ->label(__('organizations.infolist.fields.description_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('description_en')
                            ->label(__('organizations.infolist.fields.description_en'))
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('organizations.infolist.sections.branding'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('logo_path')
                            ->label(__('organizations.infolist.fields.logo'))
                            ->square()
                            ->size(150),

                        ImageEntry::make('logo_white_path')
                            ->label(__('organizations.infolist.fields.logo_white'))
                            ->square()
                            ->size(150),
                    ]),

                Section::make(__('organizations.infolist.sections.manager'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('manager_name_ar')
                            ->label(__('organizations.infolist.fields.manager_name_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('manager_name_en')
                            ->label(__('organizations.infolist.fields.manager_name_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('manager_message_ar')
                            ->label(__('organizations.infolist.fields.manager_message_ar'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('manager_message_en')
                            ->label(__('organizations.infolist.fields.manager_message_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        ImageEntry::make('manager_image_path')
                            ->label(__('organizations.infolist.fields.manager_image'))
                            ->circular()
                            ->size(120)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('organizations.infolist.sections.links'))
                    ->icon('heroicon-o-link')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('website_url')
                            ->label(__('organizations.infolist.fields.website_url'))
                            ->icon('heroicon-o-globe-alt')
                            ->url(fn ($record) => $record->website_url, shouldOpenInNewTab: true)
                            ->placeholder(__('common.placeholders.not_provided')),
                    ]),

                Section::make(__('organizations.infolist.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('organizations.infolist.fields.is_published'))
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
