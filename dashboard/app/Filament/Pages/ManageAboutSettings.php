<?php

namespace App\Filament\Pages;

use App\Enums\FileStoragePath;
use App\Settings\AboutSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageAboutSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?int $navigationSort = 3;

    protected static string $settings = AboutSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.pages.about');
    }

    public function getTitle(): string
    {
        return __('settings.pages.about');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('settings.sections.intro'))
                    ->description(__('settings.sections.intro_description'))
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Textarea::make('intro_text_ar')
                            ->label(__('settings.fields.intro_text_ar'))
                            ->required()
                            ->rows(4)
                            ->placeholder(__('settings.placeholders.intro_text_ar')),

                        Textarea::make('intro_text_en')
                            ->label(__('settings.fields.intro_text_en'))
                            ->required()
                            ->rows(4)
                            ->placeholder(__('settings.placeholders.intro_text_en')),
                    ]),

                Section::make(__('settings.sections.intro_images'))
                    ->description(__('settings.sections.intro_images_description'))
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('intro_image_one')
                            ->label(__('settings.fields.intro_image_one'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public'),

                        FileUpload::make('intro_image_two')
                            ->label(__('settings.fields.intro_image_two'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public'),

                        FileUpload::make('intro_image_three')
                            ->label(__('settings.fields.intro_image_three'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public'),

                        FileUpload::make('intro_image_four')
                            ->label(__('settings.fields.intro_image_four'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public'),
                    ]),

                Section::make(__('settings.sections.vision_message_values'))
                    ->description(__('settings.sections.vision_message_values_description'))
                    ->icon('heroicon-o-eye')
                    ->columns(2)
                    ->schema([
                        Textarea::make('vision_ar')
                            ->label(__('settings.fields.vision_ar'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.vision_ar')),

                        Textarea::make('vision_en')
                            ->label(__('settings.fields.vision_en'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.vision_en')),

                        Textarea::make('message_ar')
                            ->label(__('settings.fields.message_ar'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.message_ar')),

                        Textarea::make('message_en')
                            ->label(__('settings.fields.message_en'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.message_en')),

                        Textarea::make('values_ar')
                            ->label(__('settings.fields.values_ar'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.values_ar')),

                        Textarea::make('values_en')
                            ->label(__('settings.fields.values_en'))
                            ->required()
                            ->rows(3)
                            ->placeholder(__('settings.placeholders.values_en')),
                    ]),

                Section::make(__('settings.sections.founder'))
                    ->description(__('settings.sections.founder_description'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        Textarea::make('founder_message_ar')
                            ->label(__('settings.fields.founder_message_ar'))
                            ->required()
                            ->rows(4)
                            ->placeholder(__('settings.placeholders.founder_message_ar')),

                        Textarea::make('founder_message_en')
                            ->label(__('settings.fields.founder_message_en'))
                            ->required()
                            ->rows(4)
                            ->placeholder(__('settings.placeholders.founder_message_en')),

                        TextInput::make('founder_name_ar')
                            ->label(__('settings.fields.founder_name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.founder_name_ar')),

                        TextInput::make('founder_name_en')
                            ->label(__('settings.fields.founder_name_en'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.founder_name_en')),

                        TextInput::make('founder_position_ar')
                            ->label(__('settings.fields.founder_position_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.founder_position_ar')),

                        TextInput::make('founder_position_en')
                            ->label(__('settings.fields.founder_position_en'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('settings.placeholders.founder_position_en')),

                        FileUpload::make('founder_photo')
                            ->label(__('settings.fields.founder_photo'))
                            ->image()
                            ->imageEditor()
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('settings.sections.profile_document'))
                    ->description(__('settings.sections.profile_document_description'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        FileUpload::make('profile_document')
                            ->label(__('settings.fields.profile_document'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory(FileStoragePath::SettingsImages->value)
                            ->visibility('public')
                            ->helperText(__('settings.helpers.profile_document'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
