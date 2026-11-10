<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;

/**
 * A translatable field as an Arabic input (required unless told otherwise) next to an optional English
 * input. Works with spatie/laravel-translatable models (data is ['ar' => ..., 'en' => ...]) and with
 * settings stored the same way.
 */
class Translatable
{
    public static function text(string $name, string $label, bool $required = true, ?int $maxLength = 255): Grid
    {
        return Grid::make(2)->schema([
            TextInput::make("$name.ar")->label($label)->required($required)->maxLength($maxLength),
            TextInput::make("$name.en")->label($label.' (English)')->maxLength($maxLength)
                ->extraInputAttributes(['dir' => 'ltr']),
        ])->columnSpanFull();
    }

    public static function textarea(string $name, string $label, bool $required = false, int $rows = 3): Grid
    {
        return Grid::make(2)->schema([
            Textarea::make("$name.ar")->label($label)->required($required)->rows($rows),
            Textarea::make("$name.en")->label($label.' (English)')->rows($rows)
                ->extraInputAttributes(['dir' => 'ltr']),
        ])->columnSpanFull();
    }
}
