<?php

namespace App\Filament\Pages;

use App\Settings\CompanySettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class CompanySettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = CompanySettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.company_settings');
    }

    public function getTitle(): string
    {
        return __('app.company_settings');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.name'))
                            ->required(),
                        TextInput::make('address')
                            ->label(__('app.address'))
                            ->required(),
                        TextInput::make('phone')
                            ->label(__("app.phone"))
                            ->tel()
                            ->required(),
                        TextInput::make('email')
                            ->label(__("app.email"))
                            ->email()
                            ->required(),
                        TextInput::make('about_us_document')
                            ->label(__("app.about_us_document"))
                            ->url()
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make('')
                    ->schema([
                        Repeater::make('socialMediaAccounts')
                            ->schema([
                                TextInput::make('url')
                                    ->label(__('app.url'))
                                    ->url()
                                    ->required(),
                                FileUpload::make('icon')
                                    ->image()
                                    ->imageEditor()
                                    ->columnSpanFull()
                                    ->maxSize(1024 * 5)
                                    ->required()
                            ])
                    ]),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label(__("app.logo"))
                            ->directory('company')
                            ->image()
                            ->maxSize(1024 * 5)
                            ->required()
                            ->columnSpanFull()
                    ]),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\FileUpload::make('map_image')
                            ->label(__("app.map_image"))
                            ->directory('company')
                            ->image()
                            ->maxSize(1024 * 5)
                            ->required()
                            ->columnSpanFull()
                    ]),
            ]);
    }
}
