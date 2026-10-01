<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public ?string $hero_image = null;

    public ?string $quote_ar = null;

    public ?string $quote_en = null;

    public ?string $quote_author_ar = null;

    public ?string $quote_author_en = null;

    public static function group(): string
    {
        return 'home';
    }
}
