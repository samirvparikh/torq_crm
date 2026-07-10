<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class LeadRepository implements LeadRepositoryInterface
{
    public function findById(int $id): ?Lead
    {
        return Lead::query()->with(['leadSource', 'assignee', 'category'])->find($id);
    }

    public function findByLeadNumber(string $leadNumber): ?Lead
    {
        return Lead::query()->where('lead_number', $leadNumber)->first();
    }

    public function findByIndiamartId(string $indiamartLeadId): ?Lead
    {
        return Lead::query()->where('indiamart_lead_id', $indiamartLeadId)->first();
    }

    public function create(array $data): Lead
    {
        return Lead::query()->create($data);
    }

    public function update(Lead $lead, array $data): Lead
    {
        $lead->update($data);

        return $lead->fresh();
    }

    public function delete(Lead $lead): bool
    {
        return (bool) $lead->delete();
    }

    public function paginate(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Lead::query()
            ->with(['leadSource', 'assignee', 'creator', 'category'])
            ->latest('id');

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    public function findDuplicate(?string $indiamartLeadId, ?string $mobile, ?string $email, ?string $companyName): ?Lead
    {
        if ($indiamartLeadId) {
            $existing = $this->findByIndiamartId($indiamartLeadId);
            if ($existing) {
                return $existing;
            }
        }

        $query = Lead::query();

        $query->where(function (Builder $builder) use ($mobile, $email, $companyName) {
            $hasCondition = false;

            if ($mobile) {
                $builder->where('mobile', $mobile);
                $hasCondition = true;
            }

            if ($email) {
                $hasCondition
                    ? $builder->orWhere('email', $email)
                    : $builder->where('email', $email);
                $hasCondition = true;
            }

            if ($companyName) {
                $hasCondition
                    ? $builder->orWhere('company_name', $companyName)
                    : $builder->where('company_name', $companyName);
            }
        });

        return $query->first();
    }

    public function getNextLeadNumberSequence(): int
    {
        $lastId = Lead::withTrashed()->max('id');

        return ($lastId ?? 0) + 1;
    }

    /**
     * @param  Builder<Lead>  $query
     * @param  array<string, mixed>  $filters
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('lead_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['lead_source_id'])) {
            $query->where('lead_source_id', $filters['lead_source_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        if (! empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['assigned_only']) && ! empty($filters['user_id'])) {
            $query->where('assigned_to', $filters['user_id']);
        }
    }
}
