<?php

namespace App\Filament\Resources\OrganizationTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('organization_types.infolist.sections.identity'))
                    ->icon('heroicon-o-tag')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title_ar')
                            ->label(__('organization_types.infolist.fields.title_ar'))
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('title_en')
                            ->label(__('organization_types.infolist.fields.title_en'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        TextEntry::make('organizations_count')
                            ->label(__('organization_types.infolist.fields.organizations_count'))
                            ->state(fn ($record) => $record->organizations()->count())
                            ->badge(),
                    ]),

                Section::make(__('organization_types.infolist.sections.publishing'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('slug')
                            ->label(__('common.fields.slug'))
                            ->placeholder(__('common.placeholders.not_provided')),

                        IconEntry::make('is_published')
                            ->label(__('organization_types.infolist.fields.is_published'))
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
