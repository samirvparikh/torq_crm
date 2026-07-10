<?php

namespace App\Models;

use App\Enums\ApiLogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    /** @use HasFactory<\Database\Factories\ApiLogFactory> */
    use HasFactory;

    protected $fillable = [
        'provider',
        'endpoint',
        'method',
        'lead_id',
        'request_headers',
        'request_body',
        'response_body',
        'response_code',
        'response_time_ms',
        'status',
        'retry_count',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'request_headers' => 'array',
            'status' => ApiLogStatus::class,
            'response_code' => 'integer',
            'response_time_ms' => 'integer',
            'retry_count' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
