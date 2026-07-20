<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            LeadSourceSeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,
            QuotationMasterSeeder::class,
            SampleMachineProductSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
