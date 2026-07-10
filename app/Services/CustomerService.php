<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $contactPersons = $data['contact_persons'] ?? [];
            $addresses = $data['addresses'] ?? [];
            unset($data['contact_persons'], $data['addresses']);

            $customer = $this->customerRepository->create($data);

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

            return $this->customerRepository->update($customer, $data)
                ->load(['company', 'contactPersons', 'addresses']);
        });
    }

    public function delete(Customer $customer): bool
    {
        return $this->customerRepository->delete($customer);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }
}
