<?php

namespace App\Events;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public ?LeadStatus $fromStatus,
        public LeadStatus $toStatus,
        public User $changedBy,
    ) {}
}
