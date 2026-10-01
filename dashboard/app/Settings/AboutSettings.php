<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public ?string $intro_text_ar = null;

    public ?string $intro_text_en = null;

    public ?string $intro_image_one = null;

    public ?string $intro_image_two = null;

    public ?string $intro_image_three = null;

    public ?string $intro_image_four = null;

    public ?string $vision_ar = null;

    public ?string $vision_en = null;

    public ?string $message_ar = null;

    public ?string $message_en = null;

    public ?string $values_ar = null;

    public ?string $values_en = null;

    public ?string $founder_message_ar = null;

    public ?string $founder_message_en = null;

    public ?string $founder_name_ar = null;

    public ?string $founder_name_en = null;

    public ?string $founder_position_ar = null;

    public ?string $founder_position_en = null;

    public ?string $founder_photo = null;

    public ?string $profile_document = null;

    public static function group(): string
    {
        return 'about';
    }
}
