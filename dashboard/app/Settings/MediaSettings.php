<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MediaSettings extends Settings
{
    public ?string $intro_text_ar = null;

    public ?string $intro_text_en = null;

    public static function group(): string
    {
        return 'media';
    }
}
