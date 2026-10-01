<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.intro_text_ar', '');
        $this->migrator->add('about.intro_text_en', '');
        $this->migrator->add('about.intro_image_one', '');
        $this->migrator->add('about.intro_image_two', '');
        $this->migrator->add('about.intro_image_three', '');
        $this->migrator->add('about.intro_image_four', '');
        $this->migrator->add('about.vision_ar', '');
        $this->migrator->add('about.vision_en', '');
        $this->migrator->add('about.message_ar', '');
        $this->migrator->add('about.message_en', '');
        $this->migrator->add('about.values_ar', '');
        $this->migrator->add('about.values_en', '');
        $this->migrator->add('about.founder_message_ar', '');
        $this->migrator->add('about.founder_message_en', '');
        $this->migrator->add('about.founder_name_ar', '');
        $this->migrator->add('about.founder_name_en', '');
        $this->migrator->add('about.founder_position_ar', '');
        $this->migrator->add('about.founder_position_en', '');
        $this->migrator->add('about.founder_photo', '');
        $this->migrator->add('about.profile_document', '');
    }
};
