<?php

namespace App\Repositories\Contracts;

use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LeadRepositoryInterface
{
    public function findById(int $id): ?Lead;

    public function findByLeadNumber(string $leadNumber): ?Lead;

    public function findByIndiamartId(string $indiamartLeadId): ?Lead;

    public function create(array $data): Lead;

    public function update(Lead $lead, array $data): Lead;

    public function delete(Lead $lead): bool;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 25): LengthAwarePaginator;

    public function findDuplicate(?string $indiamartLeadId, ?string $mobile, ?string $email, ?string $companyName): ?Lead;

    public function getNextLeadNumberSequence(): int;
}
