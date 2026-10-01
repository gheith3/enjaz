<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PoliciesAndPermissions extends Seeder
{
    public function run(): void
    {
        $this->generatePoliciesAndPermissions();
    }

    private function generatePoliciesAndPermissions(): void
    {
        // use artisan command to generate policies and permissions
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'dashboard',
            '--option' => 'policies_and_permissions',
        ]);
    }
}
