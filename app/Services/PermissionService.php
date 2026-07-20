<?php

namespace App\Services;

use App\Support\PermissionRegistry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = Permission::query()
            ->withCount('roles');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (! empty($filters['group'])) {
            $query->where('name', 'like', $filters['group'].'.%');
        }

        \App\Support\DatatableSort::apply($query, $filters, [
            'id', 'name', 'guard_name', 'created_at',
        ], 'name', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * @return list<string>
     */
    public function groups(): array
    {
        return Permission::query()
            ->pluck('name')
            ->map(fn (string $name) => strstr($name, '.', true) ?: 'other')
            ->unique()
            ->sort()
            ->values()
            ->all();
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
