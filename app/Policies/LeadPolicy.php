<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leads.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        if (! $user->can('leads.view')) {
            return false;
        }

        return $this->canAccessLead($user, $lead);
    }

    public function create(User $user): bool
    {
        return $user->can('leads.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        if (! $user->can('leads.edit')) {
            return false;
        }

        return $this->canModifyLead($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('leads.delete');
    }

    public function assign(User $user, Lead $lead): bool
    {
        return $user->can('leads.assign');
    }

    protected function canAccessLead(User $user, Lead $lead): bool
    {
        if ($user->seesUnrestrictedRecords() || $user->hasRole(\App\Enums\RoleName::Marketing->value)) {
            return true;
        }

        return (int) $lead->assigned_to === (int) $user->id
            || (int) $lead->created_by === (int) $user->id;
    }

    protected function canModifyLead(User $user, Lead $lead): bool
    {
        if ($user->seesUnrestrictedRecords()) {
            return true;
        }

        return (int) $lead->assigned_to === (int) $user->id;
    }
}
