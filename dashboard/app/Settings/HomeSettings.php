<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public string $hero_image = '';

    public string $quote_ar = '';

    public string $quote_en = '';

    public string $quote_author_ar = '';

    public string $quote_author_en = '';

    public static function group(): string
    {
        return 'home';
    }
}
