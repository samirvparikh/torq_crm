<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    /** @use HasFactory<\Database\Factories\WebhookLogFactory> */
    use HasFactory;

    protected $fillable = [
        'provider',
        'event_type',
        'lead_id',
        'headers',
        'payload',
        'status',
        'processed_at',
        'error_message',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
