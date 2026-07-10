<?php

namespace App\Services;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function generateQuotationNumber(): string
    {
        $prefix = Setting::getValue('general', 'quotation_number_prefix', 'QT');
        $sequence = (Quotation::withTrashed()->max('id') ?? 0) + 1;

        return sprintf('%s-%s', $prefix, str_pad((string) $sequence, 6, '0', STR_PAD_LEFT));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            $data['quotation_number'] = $this->generateQuotationNumber();
            $data['status'] = $data['status'] ?? QuotationStatus::Draft->value;

            $quotation = Quotation::query()->create($data);

            $this->syncItems($quotation, $items);
            $this->recalculateTotals($quotation);

            return $quotation->fresh(['items', 'lead', 'customer', 'company']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            $quotation->update($data);

            if (is_array($items)) {
                $quotation->items()->delete();
                $this->syncItems($quotation, $items);
            }

            $this->recalculateTotals($quotation);

            return $quotation->fresh(['items', 'lead', 'customer', 'company']);
        });
    }

    public function delete(Quotation $quotation): bool
    {
        return (bool) $quotation->delete();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Quotation::query()
            ->with(['lead', 'customer', 'creator'])
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('quotation_number', 'like', "%{$search}%");
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    protected function syncItems(Quotation $quotation, array $items): void
    {
        foreach ($items as $index => $item) {
            $item['sort_order'] = $item['sort_order'] ?? $index + 1;
            $quotation->items()->create($item);
        }
    }

    protected function recalculateTotals(Quotation $quotation): void
    {
        $quotation->load('items');

        $subtotal = $quotation->items->sum(fn (QuotationItem $item) => (float) $item->quantity * (float) $item->unit_price);
        $taxAmount = $quotation->items->sum('tax_amount');
        $discountAmount = (float) $quotation->discount_amount;

        $quotation->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => max(0, $subtotal + $taxAmount - $discountAmount),
        ]);
    }
}
