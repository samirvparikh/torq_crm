<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionRegistry::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach (PermissionRegistry::rolePermissions() as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }

        $this->migrateLegacyRoles();
        $this->removeObsoleteRoles();

        $this->command?->info('Roles seeded: '.implode(', ', RoleName::values()));
    }

    protected function migrateLegacyRoles(): void
    {
        $map = [
            'Sales Manager' => RoleName::Manager->value,
            'Sales Executive' => RoleName::Manager->value,
            'Tele Caller' => RoleName::Manager->value,
            'Viewer' => RoleName::Marketing->value,
        ];

        foreach ($map as $oldName => $newName) {
            $oldRole = Role::query()->where('name', $oldName)->where('guard_name', 'web')->first();
            $newRole = Role::query()->where('name', $newName)->where('guard_name', 'web')->first();

            if (! $oldRole || ! $newRole) {
                continue;
            }

            foreach ($oldRole->users as $user) {
                $user->syncRoles([$newName]);
            }
        }
    }

    protected function removeObsoleteRoles(): void
    {
        $allowed = RoleName::values();

        Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', $allowed)
            ->get()
            ->each(function (Role $role) {
                $role->syncPermissions([]);
                $role->delete();
            });
    }
}
