<?php

declare(strict_types=1);

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Enums\FileStoragePath;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('team_members.form.sections.member'))
                    ->description(__('team_members.form.sections.member_description'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name_ar')
                            ->label(__('common.translations.name_ar'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('team_members.form.placeholders.name_ar')),

                        TextInput::make('name_en')
                            ->label(__('common.translations.name_en'))
                            ->maxLength(255)
                            ->placeholder(__('team_members.form.placeholders.name_en')),

                        TextInput::make('position_ar')
                            ->label(__('team_members.form.fields.position_ar'))
                            ->maxLength(255)
                            ->placeholder(__('team_members.form.placeholders.position_ar')),

                        TextInput::make('position_en')
                            ->label(__('team_members.form.fields.position_en'))
                            ->maxLength(255)
                            ->placeholder(__('team_members.form.placeholders.position_en')),
                    ]),

                Section::make(__('team_members.form.sections.photo'))
                    ->description(__('team_members.form.sections.photo_description'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('photo_path')
                            ->label(__('team_members.form.fields.photo'))
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['333:359'])
                            ->directory(FileStoragePath::TeamMemberPhoto->value)
                            ->visibility('public')
                            ->helperText(__('team_members.form.helpers.photo')),
                    ]),

                Section::make(__('team_members.form.sections.publishing'))
                    ->description(__('team_members.form.sections.publishing_description'))
                    ->icon('heroicon-o-globe-alt')
                    ->columns(3)
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('common.fields.slug'))
                            ->unique(ignoreRecord: true)
                            ->nullable()
                            ->maxLength(255)
                            ->helperText(__('common.helpers.slug_auto')),

                        Toggle::make('is_published')
                            ->label(__('team_members.form.fields.is_published')),

                        TextInput::make('sort_order')
                            ->label(__('common.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
