<?php

namespace App\Models;

use App\Enums\LeadPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'lead_id',
        'customer_id',
        'assigned_to',
        'assigned_by',
        'priority',
        'status',
        'due_date',
        'reminder_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => LeadPriority::class,
            'status' => TaskStatus::class,
            'due_date' => 'datetime',
            'reminder_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === TaskStatus::Pending
            && $this->due_date
            && $this->due_date->isPast();
    }
}
