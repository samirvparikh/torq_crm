<?php

namespace App\Services;

use App\Enums\QuotationStatus;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationTermTemplate;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function __construct(
        protected QuotationPdfService $pdfService,
    ) {}

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

            $data = $this->applyDefaults($data);
            $data['quotation_number'] = $this->generateQuotationNumber();
            $data['status'] = $data['status'] ?? QuotationStatus::Draft->value;

            $quotation = Quotation::query()->create($data);

            $this->syncItems($quotation, $items);
            $this->recalculateTotals($quotation);

            return $quotation->fresh(['items', 'lead', 'customer', 'company', 'termTemplate']);
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

            if (! empty($data['quotation_term_template_id']) && empty($data['terms'])) {
                $data['terms'] = QuotationTermTemplate::query()->find($data['quotation_term_template_id'])?->content;
            }

            if ($data !== []) {
                $quotation->update($data);
            }

            if (is_array($items)) {
                $quotation->items()->delete();
                $this->syncItems($quotation, $items);
            }

            $this->recalculateTotals($quotation);

            return $quotation->fresh(['items', 'lead', 'customer', 'company', 'termTemplate']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateItem(Quotation $quotation, int $itemId, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $itemId, $data) {
            $item = $quotation->items()->whereKey($itemId)->firstOrFail();

            foreach (['technical_specifications', 'input_specifications', 'salient_features', 'utility_requirements'] as $field) {
                if (! array_key_exists($field, $data)) {
                    continue;
                }
                if (is_string($data[$field])) {
                    $raw = trim($data[$field]);
                    $data[$field] = $raw === '' ? null : json_decode($raw, true);
                }
            }

            if (isset($data['quantity']) || isset($data['unit_price']) || isset($data['tax_rate']) || isset($data['discount'])) {
                $qty = (float) ($data['quantity'] ?? $item->quantity);
                $price = (float) ($data['unit_price'] ?? $item->unit_price);
                $taxRate = (float) ($data['tax_rate'] ?? $item->tax_rate);
                $discount = (float) ($data['discount'] ?? $item->discount);
                $lineBase = max(0, ($qty * $price) - $discount);
                $data['tax_amount'] = round($lineBase * ($taxRate / 100), 2);
                $data['total'] = round($lineBase + $data['tax_amount'], 2);
            }

            $item->update($data);
            $this->recalculateTotals($quotation);

            return $quotation->fresh(['items', 'lead', 'customer', 'company', 'termTemplate']);
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
            ->with(['lead', 'customer', 'creator']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        \App\Support\DatatableSort::apply($query, $filters, [
            'id', 'quotation_number', 'subject', 'quotation_date', 'status', 'total', 'created_at',
        ], 'id', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function applyDefaults(array $data): array
    {
        $data['tax_type'] = $data['tax_type'] ?? 'igst';
        $data['signatory_name'] = $data['signatory_name']
            ?? Setting::getValue('company', 'signatory_name');
        $data['signatory_phone'] = $data['signatory_phone']
            ?? Setting::getValue('company', 'signatory_phone');
        $data['intro_text'] = $data['intro_text']
            ?? Setting::getValue('company', 'intro_text');

        if (empty($data['terms']) && empty($data['quotation_term_template_id'])) {
            $default = QuotationTermTemplate::query()->where('is_default', true)->where('is_active', true)->first();
            if ($default) {
                $data['quotation_term_template_id'] = $default->id;
                $data['terms'] = $default->content;
            }
        } elseif (! empty($data['quotation_term_template_id']) && empty($data['terms'])) {
            $data['terms'] = QuotationTermTemplate::query()->find($data['quotation_term_template_id'])?->content;
        }

        return $data;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    protected function syncItems(Quotation $quotation, array $items): void
    {
        foreach ($items as $index => $item) {
            $product = ! empty($item['product_id'])
                ? Product::query()->find($item['product_id'])
                : null;

            if ($product && ($item['include_catalog'] ?? true)) {
                $item = $this->pdfService->snapshotProductCatalog($item, $product);
            }

            $qty = (float) ($item['quantity'] ?? 1);
            $price = (float) ($item['unit_price'] ?? 0);
            $taxRate = (float) ($item['tax_rate'] ?? 0);
            $discount = (float) ($item['discount'] ?? 0);
            $lineBase = max(0, ($qty * $price) - $discount);
            $taxAmount = round($lineBase * ($taxRate / 100), 2);

            $item['sort_order'] = $item['sort_order'] ?? $index + 1;
            $item['tax_amount'] = $taxAmount;
            $item['total'] = round($lineBase + $taxAmount, 2);
            $item['include_catalog'] = (bool) ($item['include_catalog'] ?? true);

            $quotation->items()->create($item);
        }
    }

    protected function recalculateTotals(Quotation $quotation): void
    {
        $quotation->load('items');

        $subtotal = $quotation->items->sum(function (QuotationItem $item) {
            return max(0, ((float) $item->quantity * (float) $item->unit_price) - (float) $item->discount);
        });

        $taxAmount = $quotation->items->sum(fn (QuotationItem $item) => (float) $item->tax_amount);

        $discountAmount = (float) $quotation->discount_amount;
        if (($quotation->discount_type === 'percent' || $quotation->discount_type === 'percentage')
            && (float) $quotation->discount_value > 0) {
            $discountAmount = round($subtotal * ((float) $quotation->discount_value / 100), 2);
        } elseif ($quotation->discount_type === 'fixed' && (float) $quotation->discount_value > 0) {
            $discountAmount = (float) $quotation->discount_value;
        }

        $quotation->update([
            'subtotal' => round($subtotal, 2),
            'discount_amount' => $discountAmount,
            'tax_amount' => round($taxAmount, 2),
            'total' => max(0, round($subtotal + $taxAmount - $discountAmount, 2)),
        ]);
    }
}
