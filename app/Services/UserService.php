<?php

namespace App\Services;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = User::query()
            ->with('roles')
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (! empty($filters['role'])) {
            $query->role($filters['role']);
        }

        return $query->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $role = $data['role'] ?? null;
            unset($data['role'], $data['password_confirmation']);

            $data['email_verified_at'] = $data['email_verified_at'] ?? now();
            $data['is_active'] = array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : true;

            $user = User::query()->create($data);

            if ($role) {
                $user->syncRoles([$role]);
            }

            return $user->fresh('roles');
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $role = $data['role'] ?? null;
            unset($data['role'], $data['password_confirmation']);

            if (array_key_exists('password', $data) && empty($data['password'])) {
                unset($data['password']);
            }

            if (array_key_exists('is_active', $data)) {
                $data['is_active'] = (bool) $data['is_active'];
            }

            $user->update($data);

            if ($role !== null) {
                if (
                    $user->isSuperAdmin()
                    && $role !== RoleName::SuperAdmin->value
                    && User::role(RoleName::SuperAdmin->value)->count() <= 1
                ) {
                    throw new InvalidArgumentException('Cannot remove the last Super Admin role.');
                }

                $user->syncRoles([$role]);
            }

            return $user->fresh('roles');
        });
    }

    public function delete(User $user): bool
    {
        if ($user->isSuperAdmin() && User::role(RoleName::SuperAdmin->value)->count() <= 1) {
            throw new InvalidArgumentException('Cannot delete the last Super Admin.');
        }

        return (bool) $user->delete();
    }
}
