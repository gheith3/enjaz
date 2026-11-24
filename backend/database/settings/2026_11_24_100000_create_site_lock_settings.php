<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site_lock.enabled', false);
        $this->migrator->add('site_lock.code', null);
    }
};
