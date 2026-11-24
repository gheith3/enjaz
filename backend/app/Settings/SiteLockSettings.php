<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * Before the launch the public site can be locked: visitors see the «قريبًا» page; the team enters a 6-digit code.
 * The dashboard, the achievers' review links and the MCP keep working. Edited in «قفل الموقع قبل التدشين».
 */
class SiteLockSettings extends Settings
{
    public bool $enabled;

    public ?string $code;

    public static function group(): string
    {
        return 'site_lock';
    }
}
