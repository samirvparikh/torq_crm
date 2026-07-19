<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Support\PermissionRegistry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Role::query()
            ->withCount(['permissions', 'users'])
            ->with('permissions:id,name')
            ->orderBy('name');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $role = Role::query()->create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            if ($permissions) {
                $role->syncPermissions($permissions);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role->load('permissions:id,name')->loadCount(['permissions', 'users']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            if ($role->name === RoleName::SuperAdmin->value && isset($data['name']) && $data['name'] !== $role->name) {
                throw new InvalidArgumentException('Super Admin role name cannot be changed.');
            }

            $permissions = $data['permissions'] ?? null;
            unset($data['permissions']);

            if (isset($data['name'])) {
                $role->name = $data['name'];
                $role->save();
            }

            if (is_array($permissions)) {
                $role->syncPermissions($permissions);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return $role->fresh(['permissions:id,name'])->loadCount(['permissions', 'users']);
        });
    }

    public function delete(Role $role): bool
    {
        if (in_array($role->name, [RoleName::SuperAdmin->value, RoleName::Admin->value], true)) {
            throw new InvalidArgumentException('System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            throw new InvalidArgumentException('Cannot delete a role that is assigned to users.');
        }

        $deleted = (bool) $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $deleted;
    }

    /**
     * @return list<array{group: string, permissions: list<array{id: int, name: string}>}>
     */
    public function permissionGroups(): array
    {
        $permissions = Permission::query()->orderBy('name')->get(['id', 'name']);

        $grouped = [];
        foreach ($permissions as $permission) {
            $group = strstr($permission->name, '.', true) ?: 'other';
            $grouped[$group][] = [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        }

        $result = [];
        foreach ($grouped as $group => $items) {
            $result[] = [
                'group' => ucfirst($group),
                'permissions' => $items,
            ];
        }

        return $result;
    }

    public function syncFromRegistry(): int
    {
        $created = 0;

        foreach (PermissionRegistry::all() as $name) {
            $permission = Permission::findOrCreate($name, 'web');
            if ($permission->wasRecentlyCreated) {
                $created++;
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $created;
    }
}
