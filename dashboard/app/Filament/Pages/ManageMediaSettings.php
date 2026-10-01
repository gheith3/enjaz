<?php

namespace App\Filament\Pages;

use App\Settings\MediaSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageMediaSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 4;

    protected static string $settings = MediaSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.pages.media');
    }

    public function getTitle(): string
    {
        return __('settings.pages.media');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('settings.sections.intro'))
                    ->description(__('settings.sections.media_intro_description'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        Textarea::make('intro_text_ar')
                            ->label(__('settings.fields.intro_text_ar'))
                            ->rows(4)
                            ->placeholder(__('settings.placeholders.intro_text_ar')),

                        Textarea::make('intro_text_en')
                            ->label(__('settings.fields.intro_text_en'))
                            ->rows(4)
                            ->placeholder(__('settings.placeholders.intro_text_en')),
                    ]),
            ]);
    }
}
