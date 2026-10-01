<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.hero_image', '');
        $this->migrator->add('home.quote_ar', '');
        $this->migrator->add('home.quote_en', '');
        $this->migrator->add('home.quote_author_ar', '');
        $this->migrator->add('home.quote_author_en', '');
    }
};
