<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $logo = '';

    public string $name_ar = '';

    public string $name_en = '';

    public string $address_ar = '';

    public string $address_en = '';

    public string $phone = '';

    public ?string $phone_secondary = null;

    public string $email = '';

    public array $social_media_accounts = [];

    public string $map_image = '';

    public string $map_url = '';

    public static function group(): string
    {
        return 'general';
    }
}
