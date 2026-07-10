<?php

namespace App\Services;

use App\Enums\ActivityType;
use App\Enums\LeadStatus;
use App\Events\LeadAssigned;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadAssignmentService
{
    public function assign(Lead $lead, User $assignee, User $assignedBy, ?string $notes = null): Lead
    {
        return DB::transaction(function () use ($lead, $assignee, $assignedBy, $notes) {
            LeadAssignment::query()
                ->where('lead_id', $lead->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            LeadAssignment::query()->create([
                'lead_id' => $lead->id,
                'assigned_to' => $assignee->id,
                'assigned_by' => $assignedBy->id,
                'assigned_at' => now(),
                'notes' => $notes,
                'is_current' => true,
            ]);

            $lead->update([
                'assigned_to' => $assignee->id,
                'status' => $lead->status === LeadStatus::New ? LeadStatus::Assigned->value : $lead->status,
            ]);

            LeadActivity::query()->create([
                'lead_id' => $lead->id,
                'type' => ActivityType::Assigned->value,
                'title' => ActivityType::Assigned->value,
                'description' => "Lead assigned to {$assignee->name}",
                'properties' => ['assigned_to' => $assignee->id],
                'causer_id' => $assignedBy->id,
                'ip_address' => request()->ip(),
            ]);

            $lead = $lead->fresh();
            LeadAssigned::dispatch($lead, $assignee, $assignedBy);

            return $lead;
        });
    }
}
