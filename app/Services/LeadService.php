<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\LeadStatus;
use App\Events\LeadAssigned;
use App\Events\LeadCreated;
use App\Events\LeadStatusChanged;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadStatusLog;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LeadService
{
    public function __construct(
        protected LeadRepositoryInterface $leadRepository,
        protected LeadAssignmentService $assignmentService,
    ) {}

    public function generateLeadNumber(): string
    {
        $prefix = Setting::getValue('general', 'lead_number_prefix', config('leadcrm.lead_number_prefix', 'LD'));
        $sequence = $this->leadRepository->getNextLeadNumberSequence();

        return sprintf('%s-%s', $prefix, str_pad((string) $sequence, 6, '0', STR_PAD_LEFT));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, User $creator): Lead
    {
        return DB::transaction(function () use ($data, $creator) {
            $duplicate = $this->leadRepository->findDuplicate(
                $data['indiamart_lead_id'] ?? null,
                $data['mobile'] ?? null,
                $data['email'] ?? null,
                $data['company_name'] ?? null,
            );

            if ($duplicate) {
                throw new InvalidArgumentException('A duplicate lead already exists: '.$duplicate->lead_number);
            }

            $data['lead_number'] = $this->generateLeadNumber();
            $data['created_by'] = $creator->id;
            $data['status'] = $data['status'] ?? LeadStatus::New->value;

            $lead = $this->leadRepository->create($data);

            $this->logActivity($lead, ActivityType::LeadCreated, 'Lead created', $creator);

            LeadCreated::dispatch($lead, $creator);

            if (! empty($data['assigned_to'])) {
                $assignee = User::query()->findOrFail($data['assigned_to']);
                $this->assignmentService->assign($lead, $assignee, $creator);
            }

            return $lead->fresh(['leadSource', 'assignee', 'category', 'creator']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Lead $lead, array $data, User $user): Lead
    {
        return DB::transaction(function () use ($lead, $data, $user) {
            $originalStatus = $lead->status?->value;
            $originalAssignee = $lead->assigned_to;
            $newStatus = $data['status'] ?? null;
            $newAssignee = $data['assigned_to'] ?? null;

            unset($data['status'], $data['assigned_to']);

            $lead = $this->leadRepository->update($lead, $data);

            $this->logActivity($lead, ActivityType::Edited, 'Lead updated', $user);

            if ($newStatus && $originalStatus !== $newStatus) {
                $lead = $this->changeStatus($lead, LeadStatus::from($newStatus), $user);
            }

            if ($newAssignee && (int) $newAssignee !== (int) $originalAssignee) {
                $assignee = User::query()->findOrFail($newAssignee);
                $lead = $this->assignmentService->assign($lead, $assignee, $user);
            }

            return $lead->fresh(['leadSource', 'assignee', 'category', 'creator']);
        });
    }

    public function delete(Lead $lead): bool
    {
        return $this->leadRepository->delete($lead);
    }

    public function changeStatus(Lead $lead, LeadStatus $status, User $user, ?string $notes = null): Lead
    {
        $fromStatus = $lead->status;

        $updateData = ['status' => $status->value];

        if ($status === LeadStatus::Won) {
            $updateData['won_at'] = now();
        }

        if ($status === LeadStatus::Lost) {
            $updateData['lost_at'] = now();
        }

        $lead = $this->leadRepository->update($lead, $updateData);

        LeadStatusLog::query()->create([
            'lead_id' => $lead->id,
            'from_status' => $fromStatus?->value,
            'to_status' => $status->value,
            'notes' => $notes,
            'changed_by' => $user->id,
        ]);

        $this->logActivity($lead, ActivityType::StatusChanged, "Status changed to {$status->value}", $user, [
            'from' => $fromStatus?->value,
            'to' => $status->value,
        ]);

        LeadStatusChanged::dispatch($lead, $fromStatus, $status, $user);

        return $lead;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25)
    {
        return $this->leadRepository->paginate($filters, $perPage);
    }

    public function find(int $id): ?Lead
    {
        return $this->leadRepository->findById($id);
    }

    public function logActivity(
        Lead $lead,
        ActivityType $type,
        string $description,
        ?User $user = null,
        ?array $properties = null
    ): LeadActivity {
        return LeadActivity::query()->create([
            'lead_id' => $lead->id,
            'type' => $type->value,
            'title' => $type->value,
            'description' => $description,
            'properties' => $properties,
            'causer_id' => $user?->id,
            'ip_address' => request()->ip(),
        ]);
    }
}
