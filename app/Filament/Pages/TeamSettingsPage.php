<?php

namespace App\Filament\Pages;

use App\Settings\TeamSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class TeamSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = TeamSettings::class;

    public function form(Form $form): Form
    {
        $images = ['backgroundImage', 'introImageOne', 'introImageTwo', 'introImageThree', 'introImageFour'];
        $texts = ['introText', 'vision', 'message', 'values', 'managerMessage', 'managerName', 'managerPosition'];
        $imageInputs = [];
        $textInputs = [];

        foreach ($images as $image){
            $imageInputs[] = Forms\Components\FileUpload::make($image)
                ->label(str($image)->title())
                ->image()
                ->imageEditor()
                ->maxSize(1024 * 5)
                ->nullable();
        }

        foreach ($texts as $text){
            $textInputs[] = Forms\Components\Textarea::make($text)
                ->label(str($text)->title())
                ->required();
        }

        return $form
            ->schema([
                Forms\Components\Section::make('images')
                ->schema($imageInputs)
                ->columns(2),
                Forms\Components\Section::make('text')
                    ->schema($textInputs)
                ->columns(2)
            ]);
    }
}
