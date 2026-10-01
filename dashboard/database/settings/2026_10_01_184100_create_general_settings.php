<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo', '');
        $this->migrator->add('general.name_ar', '');
        $this->migrator->add('general.name_en', '');
        $this->migrator->add('general.address_ar', '');
        $this->migrator->add('general.address_en', '');
        $this->migrator->add('general.phone', '');
        $this->migrator->add('general.phone_secondary', null);
        $this->migrator->add('general.email', '');
        $this->migrator->add('general.social_media_accounts', []);
        $this->migrator->add('general.map_image', '');
        $this->migrator->add('general.map_url', '');
    }
};
