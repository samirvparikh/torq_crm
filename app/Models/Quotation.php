<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_number',
        'lead_id',
        'customer_id',
        'company_id',
        'subject',
        'intro_text',
        'signatory_name',
        'signatory_phone',
        'quotation_date',
        'valid_until',
        'status',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_type',
        'tax_amount',
        'total',
        'terms',
        'quotation_term_template_id',
        'notes',
        'pdf_path',
        'created_by',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuotationStatus::class,
            'quotation_date' => 'date',
            'valid_until' => 'date',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function termTemplate(): BelongsTo
    {
        return $this->belongsTo(QuotationTermTemplate::class, 'quotation_term_template_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast()
            && $this->status !== QuotationStatus::Accepted;
    }
}
