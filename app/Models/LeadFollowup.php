<?php

namespace App\Models;

use App\Enums\FollowupStatus;
use App\Enums\FollowupType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadFollowup extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFollowupFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'type',
        'status',
        'subject',
        'notes',
        'scheduled_at',
        'completed_at',
        'next_followup_at',
        'outcome',
        'duration_minutes',
        'created_by',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'type' => FollowupType::class,
            'status' => FollowupStatus::class,
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'next_followup_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOverdue(): bool
    {
        return $this->status === FollowupStatus::Pending
            && $this->scheduled_at
            && $this->scheduled_at->isPast();
    }
}
