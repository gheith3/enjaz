<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');
        $this->command->newLine();

        $seeders = [
            RolesSeeder::class,
            PoliciesAndPermissions::class,
            UserSeeder::class,
            AchievementScopeSeeder::class,
            OrganizationTypeSeeder::class,
            AchievementMainTypeSeeder::class,
            DesignContentSeeder::class,
        ];

        $this->call($seeders);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed!');
    }
}
