<?php

namespace App\Settings;

use App\Support\Localized;
use Spatie\LaravelSettings\Settings;

/**
 * Defaults for <title>, description and social sharing cards. Pages and records override them.
 */
class SeoSettings extends Settings
{
    public string $title_separator;

    /** @var array<string, string> */
    public array $default_description;

    public ?string $default_og_image;

    public string $og_locale;

    public static function group(): string
    {
        return 'seo';
    }

    public function t(string $property): string
    {
        return Localized::get($this->{$property});
    }
}
