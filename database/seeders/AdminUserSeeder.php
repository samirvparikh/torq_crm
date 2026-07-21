<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'superadmin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@gmail.com',
                'mobile' => '9999999991',
                'designation' => 'Super Administrator',
                'role' => RoleName::SuperAdmin->value,
            ],
            [
                'username' => 'admin',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@gmail.com',
                'mobile' => '9999999992',
                'designation' => 'Administrator',
                'role' => RoleName::Admin->value,
            ],
            [
                'username' => 'manager',
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager@gmail.com',
                'mobile' => '9999999993',
                'designation' => 'Manager',
                'role' => RoleName::Manager->value,
            ],
            [
                'username' => 'marketing',
                'first_name' => 'Marketing',
                'last_name' => 'User',
                'email' => 'marketing@gmail.com',
                'mobile' => '9999999996',
                'designation' => 'Marketing',
                'role' => RoleName::Marketing->value,
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $user->syncRoles([$role]);
        }

        $this->command?->info('Default users seeded. Password for all: 123456');
    }
}
