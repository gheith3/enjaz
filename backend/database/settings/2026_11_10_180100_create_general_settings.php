<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', ['ar' => 'إنجازات شباب عُمان', 'en' => 'Injazat Oman Youth']);
        $this->migrator->add('general.tagline', ['ar' => '', 'en' => '']);
        $this->migrator->add('general.logo', null);
        $this->migrator->add('general.favicon', null);
        $this->migrator->add('general.copyright', ['ar' => '', 'en' => '']);
        $this->migrator->add('general.address', ['ar' => '', 'en' => '']);
        $this->migrator->add('general.phones', []);
        $this->migrator->add('general.email', null);
        $this->migrator->add('general.theme_color', '#0C7F89');

        $this->migrator->add('seo.title_separator', ' | ');
        $this->migrator->add('seo.default_description', ['ar' => '', 'en' => '']);
        $this->migrator->add('seo.default_og_image', null);
        $this->migrator->add('seo.og_locale', 'ar_OM');
    }
};
