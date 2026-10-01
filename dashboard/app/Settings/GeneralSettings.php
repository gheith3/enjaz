<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $logo = null;

    public ?string $name_ar = null;

    public ?string $name_en = null;

    public ?string $address_ar = null;

    public ?string $address_en = null;

    public ?string $phone = null;

    public ?string $phone_secondary = null;

    public ?string $email = null;

    public array $social_media_accounts = [];

    public ?string $map_image = null;

    public ?string $map_url = null;

    public static function group(): string
    {
        return 'general';
    }
}
