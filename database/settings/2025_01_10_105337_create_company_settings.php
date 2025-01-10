<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.logo', '');
        $this->migrator->add('company.name', '');
        $this->migrator->add('company.about_us_document', '');
        $this->migrator->add('company.address', '');
        $this->migrator->add('company.phone', '');
        $this->migrator->add('company.email', '');
        $this->migrator->add('company.socialMediaAccounts', []);
    }
};
