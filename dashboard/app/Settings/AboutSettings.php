<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public string $intro_text_ar = '';

    public string $intro_text_en = '';

    public string $intro_image_one = '';

    public string $intro_image_two = '';

    public string $intro_image_three = '';

    public string $intro_image_four = '';

    public string $vision_ar = '';

    public string $vision_en = '';

    public string $message_ar = '';

    public string $message_en = '';

    public string $values_ar = '';

    public string $values_en = '';

    public string $founder_message_ar = '';

    public string $founder_message_en = '';

    public string $founder_name_ar = '';

    public string $founder_name_en = '';

    public string $founder_position_ar = '';

    public string $founder_position_en = '';

    public string $founder_photo = '';

    public string $profile_document = '';

    public static function group(): string
    {
        return 'about';
    }
}
