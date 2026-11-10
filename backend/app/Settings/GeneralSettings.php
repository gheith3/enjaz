<?php

namespace App\Settings;

use App\Support\Localized;
use Spatie\LaravelSettings\Settings;

/**
 * Site identity and contact details shown in the header, footer and meta tags.
 * Translatable values are arrays keyed by locale (['ar' => ..., 'en' => ...]); read them with t().
 * Image values are paths on the media disk (OneBox, under media/settings/).
 */
class GeneralSettings extends Settings
{
    /** @var array<string, string> */
    public array $site_name;

    /** @var array<string, string> */
    public array $tagline;

    public ?string $logo;

    public ?string $favicon;

    /** @var array<string, string> */
    public array $copyright;

    /** @var array<string, string> */
    public array $address;

    /** @var list<string> */
    public array $phones;

    public ?string $email;

    public string $theme_color;

    public static function group(): string
    {
        return 'general';
    }

    public function t(string $property): string
    {
        return Localized::get($this->{$property});
    }
}
