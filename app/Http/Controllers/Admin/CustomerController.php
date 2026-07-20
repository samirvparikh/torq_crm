<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Company;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Customer::class);

        return view('customers.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Customer::class);

        $customers = $this->customerService->list(
            $request->only(['search', 'is_active', 'sort_by', 'sort_dir']),
            (int) $request->input('per_page', 25)
        );

        return response()->json([
            'success' => true,
            'data' => CustomerResource::collection($customers->items()),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('customers.create', [
            'companies' => Company::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $customer = $this->customerService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data' => new CustomerResource($customer),
        ], 201);
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customer->load(['company', 'contactPersons', 'addresses', 'leads', 'quotations']);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('customers.edit', [
            'customer' => $customer->load(['company', 'contactPersons', 'addresses']),
            'companies' => Company::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data' => new CustomerResource($customer),
        ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        $this->customerService->delete($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ]);
    }
}
