<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationTermTemplate;
use App\Services\QuotationPdfService;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        protected QuotationService $quotationService,
        protected QuotationPdfService $pdfService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Quotation::class);

        return view('quotations.index');
    }

    public function create(): View
    {
        $this->authorize('create', Quotation::class);

        return view('quotations.create', [
            'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get([
                'id', 'name', 'price', 'tax_rate', 'unit', 'sku', 'capacity', 'description',
            ]),
            'termTemplates' => QuotationTermTemplate::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'content', 'is_default']),
        ]);
    }

    public function show(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        return view('quotations.show', array_merge(
            $this->pdfService->documentViewData($quotation),
            [
                'customers' => Customer::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
                'products' => Product::query()->where('is_active', true)->orderBy('name')->get([
                    'id', 'name', 'price', 'tax_rate', 'unit', 'sku', 'capacity', 'description',
                ]),
                'termTemplates' => QuotationTermTemplate::query()->where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'content', 'is_default']),
            ]
        ));
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Quotation::class);

        $quotations = $this->quotationService->list(
            $request->only(['search', 'status', 'sort_by', 'sort_dir']),
            (int) $request->input('per_page', 25)
        );

        return response()->json([
            'success' => true,
            'data' => $quotations->items(),
            'meta' => [
                'current_page' => $quotations->currentPage(),
                'last_page' => $quotations->lastPage(),
                'per_page' => $quotations->perPage(),
                'total' => $quotations->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Quotation::class);

        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $quotation = $this->quotationService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Quotation created successfully.',
            'data' => $quotation,
        ], 201);
    }

    public function update(Request $request, Quotation $quotation): JsonResponse
    {
        $this->authorize('update', $quotation);

        $quotation = $this->quotationService->update($quotation, $this->validated($request, partial: true));

        return response()->json([
            'success' => true,
            'message' => 'Quotation updated successfully.',
            'data' => $quotation,
        ]);
    }

    public function updateSection(Request $request, Quotation $quotation): JsonResponse
    {
        $this->authorize('update', $quotation);

        $section = $request->validate([
            'section' => ['required', 'in:details,subject,items,terms,notes,item'],
        ])['section'];

        if ($section === 'item') {
            $payload = $request->validate([
                'item_id' => ['required', 'integer', 'exists:quotation_items,id'],
                'product_name' => ['sometimes', 'required', 'string', 'max:191'],
                'capacity' => ['nullable', 'string', 'max:191'],
                'description' => ['nullable', 'string'],
                'operation' => ['nullable', 'string'],
                'technical_specifications' => ['nullable'],
                'input_specifications' => ['nullable'],
                'salient_features' => ['nullable'],
                'utility_requirements' => ['nullable'],
                'include_catalog' => ['nullable', 'boolean'],
                'quantity' => ['nullable', 'numeric', 'min:0.01'],
                'unit_price' => ['nullable', 'numeric', 'min:0'],
                'tax_rate' => ['nullable', 'numeric', 'min:0'],
            ]);

            $itemId = (int) $payload['item_id'];
            unset($payload['item_id']);

            $quotation = $this->quotationService->updateItem($quotation, $itemId, $payload);
        } else {
            $rules = match ($section) {
                'details' => [
                    'customer_id' => ['nullable', 'exists:customers,id'],
                    'quotation_date' => ['required', 'date'],
                    'valid_until' => ['nullable', 'date'],
                    'status' => ['nullable', 'string', 'max:30'],
                    'tax_type' => ['nullable', 'in:igst,cgst_sgst'],
                    'signatory_name' => ['nullable', 'string', 'max:191'],
                    'signatory_phone' => ['nullable', 'string', 'max:30'],
                ],
                'subject' => [
                    'subject' => ['nullable', 'string', 'max:255'],
                    'intro_text' => ['nullable', 'string'],
                ],
                'items' => [
                    'items' => ['required', 'array', 'min:1'],
                    'items.*.product_id' => ['nullable', 'exists:products,id'],
                    'items.*.product_name' => ['required', 'string', 'max:191'],
                    'items.*.capacity' => ['nullable', 'string', 'max:191'],
                    'items.*.description' => ['nullable', 'string'],
                    'items.*.operation' => ['nullable', 'string'],
                    'items.*.technical_specifications' => ['nullable'],
                    'items.*.input_specifications' => ['nullable'],
                    'items.*.salient_features' => ['nullable'],
                    'items.*.utility_requirements' => ['nullable'],
                    'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
                    'items.*.unit_price' => ['required', 'numeric', 'min:0'],
                    'items.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
                    'items.*.include_catalog' => ['nullable', 'boolean'],
                    'tax_type' => ['nullable', 'in:igst,cgst_sgst'],
                    'discount_type' => ['nullable', 'string', 'max:20'],
                    'discount_value' => ['nullable', 'numeric', 'min:0'],
                ],
                'terms' => [
                    'terms' => ['nullable', 'string'],
                    'quotation_term_template_id' => ['nullable', 'exists:quotation_term_templates,id'],
                ],
                'notes' => [
                    'notes' => ['nullable', 'string'],
                ],
                default => [],
            };

            $quotation = $this->quotationService->update($quotation, $request->validate($rules));
        }

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.',
            'data' => $quotation,
        ]);
    }

    public function pdf(Quotation $quotation): Response
    {
        $this->authorize('view', $quotation);

        $this->pdfService->store($quotation);

        return $this->pdfService->download($quotation);
    }

    public function preview(Quotation $quotation): Response
    {
        $this->authorize('view', $quotation);

        return $this->pdfService->stream($quotation);
    }

    public function destroy(Quotation $quotation): JsonResponse
    {
        $this->authorize('delete', $quotation);

        $this->quotationService->delete($quotation);

        return response()->json([
            'success' => true,
            'message' => 'Quotation deleted successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'lead_id' => ['nullable', 'exists:leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'subject' => ['nullable', 'string', 'max:255'],
            'intro_text' => ['nullable', 'string'],
            'signatory_name' => ['nullable', 'string', 'max:191'],
            'signatory_phone' => ['nullable', 'string', 'max:30'],
            'quotation_date' => [$partial ? 'sometimes' : 'required', 'date'],
            'valid_until' => ['nullable', 'date'],
            'tax_type' => ['nullable', 'in:igst,cgst_sgst'],
            'terms' => ['nullable', 'string'],
            'quotation_term_template_id' => ['nullable', 'exists:quotation_term_templates,id'],
            'notes' => ['nullable', 'string'],
            'discount_type' => ['nullable', 'string', 'max:20'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => [$partial ? 'sometimes' : 'required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.product_name' => ['required_with:items', 'string', 'max:191'],
            'items.*.capacity' => ['nullable', 'string', 'max:191'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
            'items.*.include_catalog' => ['nullable', 'boolean'],
        ]);
    }
}
