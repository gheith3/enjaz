<?php

namespace App\Filament\Pages;

use App\Enums\FileStoragePath;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected static string $settings = GeneralSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.pages.general');
    }

    public function getTitle(): string
    {
        return __('settings.pages.general');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('settings.sections.identity'))
                    ->description(__('settings.sections.identity_description'))
                    ->icon('heroicon-o-building-office')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name_ar')
                            ->label(__('settings.fields.name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.name_ar')),

                        TextInput::make('name_en')
                            ->label(__('settings.fields.name_en'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.name_en')),

                        TextInput::make('address_ar')
                            ->label(__('settings.fields.address_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.address_ar')),

                        TextInput::make('address_en')
                            ->label(__('settings.fields.address_en'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.address_en')),
                    ]),

                Section::make(__('settings.sections.contact_information'))
                    ->description(__('settings.sections.contact_information_description'))
                    ->icon('heroicon-o-phone')
                    ->columns(3)
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('settings.fields.phone'))
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->placeholder(__('settings.placeholders.phone')),

                        TextInput::make('phone_secondary')
                            ->label(__('settings.fields.phone_secondary'))
                            ->tel()
                            ->maxLength(20)
                            ->placeholder(__('settings.placeholders.phone')),

                        TextInput::make('email')
                            ->label(__('settings.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.email')),
                    ]),

                Section::make(__('settings.sections.branding'))
                    ->description(__('settings.sections.branding_description'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('logo')
                            ->label(__('settings.fields.logo'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('settings.sections.social_media'))
                    ->description(__('settings.sections.social_media_description'))
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Repeater::make('social_media_accounts')
                            ->label(__('settings.fields.social_media_accounts'))
                            ->addActionLabel(__('settings.fields.add_social_media_account'))
                            ->columns(2)
                            ->schema([
                                Select::make('platform')
                                    ->label(__('settings.fields.platform'))
                                    ->options([
                                        'instagram' => __('settings.social_platforms.instagram'),
                                        'twitter' => __('settings.social_platforms.twitter'),
                                        'x' => __('settings.social_platforms.x'),
                                        'linkedin' => __('settings.social_platforms.linkedin'),
                                        'facebook' => __('settings.social_platforms.facebook'),
                                        'youtube' => __('settings.social_platforms.youtube'),
                                        'tiktok' => __('settings.social_platforms.tiktok'),
                                        'other' => __('settings.social_platforms.other'),
                                    ])
                                    ->required()
                                    ->native(false),

                                TextInput::make('url')
                                    ->label(__('settings.fields.url'))
                                    ->url()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('settings.placeholders.url')),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('settings.sections.map'))
                    ->description(__('settings.sections.map_description'))
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('map_image')
                            ->label(__('settings.fields.map_image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public')
                            ->columnSpanFull(),

                        TextInput::make('map_url')
                            ->label(__('settings.fields.map_url'))
                            ->url()
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.map_url'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
