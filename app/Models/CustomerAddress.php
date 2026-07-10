<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerAddressFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'label',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'is_primary',
        'is_billing',
        'is_shipping',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_billing' => 'boolean',
            'is_shipping' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
