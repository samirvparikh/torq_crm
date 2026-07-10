<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Company::class);

        return view('companies.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Company::class);

        $companies = $this->companyService->list(
            $request->only(['search']),
            (int) $request->input('per_page', 25)
        );

        return response()->json([
            'success' => true,
            'data' => $companies->items(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Company::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gst_number' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
        ]);

        $data['created_by'] = $request->user()->id;
        $company = $this->companyService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully.',
            'data' => $company,
        ], 201);
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:191'],
            'email' => ['sometimes', 'nullable', 'email', 'max:191'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'gst_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $company = $this->companyService->update($company, $data);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully.',
            'data' => $company,
        ]);
    }

    public function destroy(Company $company): JsonResponse
    {
        $this->authorize('delete', $company);

        $this->companyService->delete($company);

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully.',
        ]);
    }
}
