<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('users.infolist.sections.personal_information'))
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('common.fields.full_name'))
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('email')
                            ->label(__('users.infolist.fields.email_address'))
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage(__('users.infolist.messages.email_copied')),

                        TextEntry::make('full_phone_number')
                            ->label(__('common.fields.phone_number'))
                            ->icon('heroicon-o-phone')
                            ->placeholder(__('common.placeholders.not_provided'))
                            ->copyable(),
                    ]),

                Section::make(__('users.infolist.sections.role_access'))
                    ->icon('heroicon-o-shield-check')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label(__('users.infolist.fields.user_type'))
                            ->badge(),
                    ]),

                Section::make(__('users.infolist.sections.activity'))
                    ->icon('heroicon-o-clock')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('users.infolist.fields.account_created'))
                            ->dateTime('F j, Y \a\t g:i A')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('updated_at')
                            ->label(__('users.infolist.fields.last_updated'))
                            ->dateTime('F j, Y \a\t g:i A')
                            ->icon('heroicon-o-pencil-square'),

                        TextEntry::make('email_verified_at')
                            ->label(__('users.infolist.fields.email_verified'))
                            ->dateTime('F j, Y \a\t g:i A')
                            ->icon('heroicon-o-check-badge')
                            ->placeholder(__('users.infolist.placeholders.not_verified')),
                    ]),
                Section::make(__('users.infolist.sections.profile'))
                    ->schema([
                        ImageEntry::make('avatar_url')
                            ->label('')
                            ->circular()
                            ->size(150)
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF&size=150'),
                    ]),

                Section::make(__('users.infolist.sections.status'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('users.infolist.fields.status'))
                            ->badge(),
                            
                        IconEntry::make('email_verified_at')
                            ->label(__('users.infolist.fields.email_verified'))
                            ->boolean()
                            ->getStateUsing(fn ($record) => $record->email_verified_at !== null)
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-mark')
                            ->trueColor('success')
                            ->falseColor('warning'),
                    ]),
            ]);
    }
}
