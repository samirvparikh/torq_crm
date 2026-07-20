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
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'phone' => '9999999991',
                'designation' => 'Super Administrator',
                'role' => RoleName::SuperAdmin->value,
            ],
            [
                'name' => 'System Admin',
                'email' => 'admin@gmail.com',
                'phone' => '9999999992',
                'designation' => 'Administrator',
                'role' => RoleName::Admin->value,
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@gmail.com',
                'phone' => '9999999993',
                'designation' => 'Manager',
                'role' => RoleName::Manager->value,
            ],
            [
                'name' => 'Marketing User',
                'email' => 'marketing@gmail.com',
                'phone' => '9999999996',
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
