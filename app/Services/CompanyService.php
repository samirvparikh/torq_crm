<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Company
    {
        return Company::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company->fresh();
    }

    public function delete(Company $company): bool
    {
        return (bool) $company->delete();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Company::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('gst_number', 'like', "%{$search}%");
            });
        }

        \App\Support\DatatableSort::apply($query, $filters, [
            'id', 'name', 'phone', 'email', 'gst_number', 'city', 'state', 'is_active', 'created_at',
        ], 'id', 'desc');

        return $query->paginate($perPage);
    }
}
