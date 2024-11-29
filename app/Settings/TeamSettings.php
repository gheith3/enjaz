<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TeamSettings extends Settings
{
    public string $backgroundImage = 'images/app/ABG.svg';
    public string $introText = "";

    public string $introImageOne = "";
    public string $introImageTwo = "";
    public string $introImageThree = "";
    public string $introImageFour = "";

    public string $vision = "";
    public string $message = "";
    public string $values = "";
    public string $managerMessage = "";
    public string $managerName = "";
    public string $managerPosition = "";

    public static function group(): string
    {
        return 'team';
    }
}
