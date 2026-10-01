<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactUsMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactUsMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('contact.infolist.sections.sender_information'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('contact.infolist.fields.name'))
                            ->icon('heroicon-o-user')
                            ->weight('bold'),

                        TextEntry::make('email')
                            ->label(__('contact.infolist.fields.email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable(),

                        TextEntry::make('ip_address')
                            ->label(__('contact.infolist.fields.ip_address'))
                            ->icon('heroicon-o-globe-alt')
                            ->fontFamily('mono')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('status')
                            ->label(__('contact.infolist.fields.status'))
                            ->badge(),
                    ]),

                Section::make(__('contact.infolist.sections.message'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        TextEntry::make('message')
                            ->label('')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('contact.infolist.sections.timestamps'))
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('contact.infolist.fields.created_at'))
                            ->dateTime('F j, Y \a\t g:i:s A')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('updated_at')
                            ->label(__('common.fields.updated_at'))
                            ->dateTime('F j, Y \a\t g:i:s A')
                            ->icon('heroicon-o-pencil-square'),
                    ]),
            ]);
    }
}
