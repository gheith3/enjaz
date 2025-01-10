<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    public string $logo = '';
    public string $about_us_document = '';
    public string $map_image = '';

    public string $name = 'انجاز عمان';
    public string $address = 'العنوان';
    public string $phone = '123456789';
    public string $email = 'info@enjaz.om';

    public array $socialMediaAccounts = [];

    public static function group(): string
    {
        return 'company';
    }

}
