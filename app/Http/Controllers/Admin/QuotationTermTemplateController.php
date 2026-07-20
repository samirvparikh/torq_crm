<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationTermTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuotationTermTemplateController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('quotations.view'), 403);

        $sortBy = (string) $request->input('sort_by', 'sort_order');
        $sortDir = strtolower((string) $request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowed = ['id', 'name', 'is_default', 'is_active', 'sort_order', 'created_at'];
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'sort_order';
            $sortDir = 'asc';
        }

        $query = QuotationTermTemplate::query()->reorder()->orderBy($sortBy, $sortDir);
        if ($sortBy !== 'name') {
            $query->orderBy('name');
        }

        return view('quotation-terms.index', [
            'templates' => $query->get(),
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('quotations.create') || auth()->user()->can('quotations.edit'), 403);

        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if (! empty($data['is_default'])) {
            QuotationTermTemplate::query()->update(['is_default' => false]);
        }

        $template = QuotationTermTemplate::query()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Terms template created.',
            'data' => $template,
        ], 201);
    }

    public function update(Request $request, QuotationTermTemplate $quotationTerm): JsonResponse
    {
        abort_unless(auth()->user()->can('quotations.edit'), 403);

        $data = $this->validated($request, $quotationTerm->id);

        if (! empty($data['is_default'])) {
            QuotationTermTemplate::query()->where('id', '!=', $quotationTerm->id)->update(['is_default' => false]);
        }

        $quotationTerm->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Terms template updated.',
            'data' => $quotationTerm->fresh(),
        ]);
    }

    public function destroy(QuotationTermTemplate $quotationTerm): JsonResponse
    {
        abort_unless(auth()->user()->can('quotations.delete'), 403);

        $quotationTerm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Terms template deleted.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:quotation_term_templates,slug'.($ignoreId ? ','.$ignoreId : '')],
            'content' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
