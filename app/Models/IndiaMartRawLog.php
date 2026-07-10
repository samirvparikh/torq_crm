<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndiaMartRawLog extends Model
{
    /** @use HasFactory<\Database\Factories\IndiaMartRawLogFactory> */
    use HasFactory;

    protected $table = 'indiamart_raw_logs';

    protected $fillable = [
        'lead_id',
        'request_json',
        'response_json',
        'status',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
