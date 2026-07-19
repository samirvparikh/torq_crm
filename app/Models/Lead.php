<?php

namespace App\Models;

use App\Enums\LeadPriority;
use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_number',
        'lead_source_id',
        'indiamart_lead_id',
        'query_type',
        'query_time',
        'query_mcat_name',
        'call_duration',
        'receiver_mobile',
        'customer_id',
        'company_id',
        'category_id',
        'customer_name',
        'company_name',
        'gst_number',
        'mobile',
        'alternate_mobile',
        'whatsapp',
        'email',
        'website',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'interested_product',
        'requirement',
        'quantity',
        'budget',
        'priority',
        'status',
        'lost_reason',
        'assigned_to',
        'created_by',
        'expected_closing_date',
        'remarks',
        'last_contacted_at',
        'next_followup_at',
        'won_at',
        'lost_at',
        'won_value',
        'raw_data',
        'is_duplicate',
        'duplicate_of_lead_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'priority' => LeadPriority::class,
            'budget' => 'decimal:2',
            'won_value' => 'decimal:2',
            'expected_closing_date' => 'date',
            'query_time' => 'datetime',
            'last_contacted_at' => 'datetime',
            'next_followup_at' => 'datetime',
            'won_at' => 'datetime',
            'lost_at' => 'datetime',
            'raw_data' => 'array',
            'is_duplicate' => 'boolean',
        ];
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'duplicate_of_lead_id');
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(self::class, 'duplicate_of_lead_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(LeadProduct::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(LeadAssignment::class);
    }

    public function currentAssignment(): ?LeadAssignment
    {
        return $this->assignments()->where('is_current', true)->latest('assigned_at')->first();
    }

    public function followups(): HasMany
    {
        return $this->hasMany(LeadFollowup::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(LeadStatusLog::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function apiLogs(): HasMany
    {
        return $this->hasMany(ApiLog::class);
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    public function indiamartRawLogs(): HasMany
    {
        return $this->hasMany(IndiaMartRawLog::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            LeadStatus::Won->value,
            LeadStatus::Lost->value,
            LeadStatus::Junk->value,
            LeadStatus::Duplicate->value,
        ]);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByStatus(Builder $query, LeadStatus|string $status): Builder
    {
        $value = $status instanceof LeadStatus ? $status->value : $status;

        return $query->where('status', $value);
    }

    public function isClosed(): bool
    {
        return $this->status?->isClosed() ?? false;
    }
}
