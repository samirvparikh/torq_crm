<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $contactPersons = $data['contact_persons'] ?? [];
            $addresses = $data['addresses'] ?? [];
            unset($data['contact_persons'], $data['addresses']);

            $customer = Customer::query()->create($data);

            foreach ($contactPersons as $person) {
                $customer->contactPersons()->create($person);
            }

            foreach ($addresses as $address) {
                $customer->addresses()->create($address);
            }

            return $customer->fresh(['company', 'contactPersons', 'addresses']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            unset($data['contact_persons'], $data['addresses']);

            $customer->update($data);

            return $customer->fresh(['company', 'contactPersons', 'addresses']);
        });
    }

    public function delete(Customer $customer): bool
    {
        return (bool) $customer->delete();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Customer::query()
            ->with(['company'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Customer
    {
        return Customer::query()->with(['company', 'contactPersons', 'addresses'])->find($id);
    }
}
