<?php

namespace App\Filament\Pages;

use App\Settings\ContactSettings;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageContactSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?int $navigationSort = 5;

    protected static string $settings = ContactSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.pages.contact');
    }

    public function getTitle(): string
    {
        return __('settings.pages.contact');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('settings.sections.contact_information'))
                    ->description(__('settings.sections.contact_page_description'))
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        TextInput::make('phone_primary')
                            ->label(__('settings.fields.phone_primary'))
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder(__('settings.placeholders.phone')),

                        TextInput::make('phone_secondary')
                            ->label(__('settings.fields.phone_secondary'))
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder(__('settings.placeholders.phone')),

                        TextInput::make('email')
                            ->label(__('settings.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.email')),

                        TextInput::make('map_url')
                            ->label(__('settings.fields.map_url'))
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.map_url')),
                    ]),

                Section::make(__('settings.sections.social_media'))
                    ->description(__('settings.sections.social_media_description'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label(__('settings.fields.instagram_url'))
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.url')),

                        TextInput::make('twitter_url')
                            ->label(__('settings.fields.twitter_url'))
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.url')),

                        TextInput::make('linkedin_url')
                            ->label(__('settings.fields.linkedin_url'))
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.url')),
                    ]),
            ]);
    }
}
