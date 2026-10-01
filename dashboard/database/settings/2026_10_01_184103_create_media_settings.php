<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('media.intro_text_ar', null);
        $this->migrator->add('media.intro_text_en', null);
    }
};
