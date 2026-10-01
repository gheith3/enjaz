<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public string $phone_primary = '';

    public string $phone_secondary = '';

    public string $email = '';

    public string $map_url = '';

    public string $instagram_url = '';

    public string $twitter_url = '';

    public string $linkedin_url = '';

    public static function group(): string
    {
        return 'contact';
    }
}
