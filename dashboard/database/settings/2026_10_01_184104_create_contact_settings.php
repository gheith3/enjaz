<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('contact.phone_primary', '');
        $this->migrator->add('contact.phone_secondary', '');
        $this->migrator->add('contact.email', '');
        $this->migrator->add('contact.map_url', '');
        $this->migrator->add('contact.instagram_url', '');
        $this->migrator->add('contact.twitter_url', '');
        $this->migrator->add('contact.linkedin_url', '');
    }
};
