<?php

namespace App\Filament\Pages;

use App\Enums\FileStoragePath;
use App\Settings\HomeSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageHomeSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 2;

    protected static string $settings = HomeSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.pages.home');
    }

    public function getTitle(): string
    {
        return __('settings.pages.home');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('settings.sections.hero'))
                    ->description(__('settings.sections.hero_description'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label(__('settings.fields.hero_image'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('settings.sections.quote'))
                    ->description(__('settings.sections.quote_description'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->columns(2)
                    ->schema([
                        Textarea::make('quote_ar')
                            ->label(__('settings.fields.quote_ar'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.quote_ar')),

                        Textarea::make('quote_en')
                            ->label(__('settings.fields.quote_en'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.quote_en')),

                        TextInput::make('quote_author_ar')
                            ->label(__('settings.fields.quote_author_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.quote_author_ar')),

                        TextInput::make('quote_author_en')
                            ->label(__('settings.fields.quote_author_en'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.quote_author_en')),
                    ]),
            ]);
    }
}
