<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('team.backgroundImage', 'images/app/ABG.svg');
        $this->migrator->add('team.introText', "");
        $this->migrator->add('team.introImageOne', "");
        $this->migrator->add('team.introImageTwo', "");
        $this->migrator->add('team.introImageThree', "");
        $this->migrator->add('team.introImageFour', "");
        $this->migrator->add('team.vision', "");
        $this->migrator->add('team.message', "");
        $this->migrator->add('team.values', "");
        $this->migrator->add('team.managerMessage', "");
        $this->migrator->add('team.managerName', "");
        $this->migrator->add('team.managerPosition', "");
    }
};
