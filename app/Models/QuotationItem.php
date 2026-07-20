<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationItemFactory> */
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_name',
        'description',
        'capacity',
        'operation',
        'technical_specifications',
        'input_specifications',
        'salient_features',
        'utility_requirements',
        'include_catalog',
        'unit',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'sort_order' => 'integer',
            'include_catalog' => 'boolean',
            'technical_specifications' => 'array',
            'input_specifications' => 'array',
            'salient_features' => 'array',
            'utility_requirements' => 'array',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
