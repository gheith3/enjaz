<?php

namespace App\Filament\Pages;

use App\Settings\TeamSettings;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class TeamSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = TeamSettings::class;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.settings');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('app.team.page.nav_title');
    }

    public function getTitle(): string
    {
        return __('app.team.page.label');
    }

    public function form(Form $form): Form
    {
        $images = ['backgroundImage', 'introImageOne', 'introImageTwo', 'introImageThree', 'introImageFour'];
        $texts = ['introText', 'vision', 'message', 'values', 'managerMessage', 'managerName', 'managerPosition'];
        $imageInputs = [];
        $textInputs = [];

        foreach ($images as $image) {
            $imageInputs[] = Forms\Components\FileUpload::make($image)
                ->label(__('app.team.fields.' . $image))
                ->image()
                ->imageEditor()
                ->maxSize(1024 * 5)
                ->nullable();
        }

        foreach ($texts as $text) {
            $textInputs[] = Forms\Components\Textarea::make($text)
                ->label(__('app.team.fields.' . $text))
                ->required();
        }

        return $form
            ->schema([
                Forms\Components\Section::make(__('app.team.sections.images'))
                    ->schema($imageInputs)
                    ->columns(2),
                Forms\Components\Section::make(__('app.team.sections.text'))
                    ->schema($textInputs)
                    ->columns(2),
                Forms\Components\Section::make(__('app.team.sections.members'))
                    ->schema([
                        TableRepeater::make('members')
                            ->label(__('app.team.fields.member'))
                            ->headers([
                                Header::make(__('app.team.fields.name')),
                                Header::make(__('app.team.fields.position')),
                                Header::make(__('app.team.fields.image')),
                            ])
                            ->orderColumn('sort_order')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('app.team.fields.name'))
                                    ->required(),
                                TextInput::make('position')
                                    ->label(__('app.team.fields.position'))
                                    ->required(),
                                FileUpload::make('image')
                                    ->label(__('app.team.fields.image'))
                                    ->previewable(false)
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(1024 * 5)
                                    ->required()
                            ])->columns(3)
                    ]),
            ]);
    }
}
