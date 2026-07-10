<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerContactPerson extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerContactPersonFactory> */
    use HasFactory;

    protected $table = 'customer_contact_persons';

    protected $fillable = [
        'customer_id',
        'name',
        'designation',
        'email',
        'mobile',
        'whatsapp',
        'is_primary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
