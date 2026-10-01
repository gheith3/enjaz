<?php

namespace Database\Seeders;

use App\Enums\AppUserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPlatformUsers();
    }

    public function createPlatformUsers(): void
    {
        if (! app()->isLocal()) {
            // super admin for production setup
            $mainAdmin = [
                [
                    'name' => 'Gheith',
                    'email' => 'ghak992@gmail.com',
                    'phone_number' => '93839229',
                    'country_code' => '968',
                ],
            ];
            foreach ($mainAdmin as $userData) {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'country_code' => $userData['country_code'],
                        'phone_number' => $userData['phone_number'],
                        'password' => Hash::make(Str::random(10)),
                        'email_verified_at' => now(),
                        'status' => UserStatus::Active,
                    ]
                );
                $user->syncRoles([AppUserRole::SuperAdmin]);
            }
        }

        if (app()->isLocal()) {
            foreach (AppUserRole::cases() as $role) {
                $user = User::firstOrCreate(
                    ['email' => $role->value.'@admin.com'],
                    [
                        'name' => $role->value,
                        'phone_number' => rand(90000000, 99999999),
                        'password' => Hash::make('P@ssw0rd'),
                        'email_verified_at' => now(),
                        'status' => UserStatus::Active,
                    ]
                );
                $user->syncRoles([$role->value]);
            }
        }
    }
}
