<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Product::class);

        return view('products.index', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->list(
            $request->only(['search', 'category_id', 'sort_by', 'sort_dir']),
            (int) $request->input('per_page', 25)
        );

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $data = $this->validatedProduct($request);
        $data['created_by'] = $request->user()->id;
        $product = $this->productService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $product = $this->productService->update($product, $this->validatedProduct($request, $product->id));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedProduct(Request $request, ?int $productId = null): array
    {
        $data = $request->validate([
            'category_id' => [$productId ? 'sometimes' : 'nullable', 'nullable', 'exists:categories,id'],
            'name' => [$productId ? 'sometimes' : 'required', 'required', 'string', 'max:191'],
            'sku' => [$productId ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:100', 'unique:products,sku'.($productId ? ','.$productId : '')],
            'description' => ['nullable', 'string'],
            'capacity' => ['nullable', 'string', 'max:191'],
            'operation' => ['nullable', 'string'],
            'technical_specifications' => ['nullable'],
            'input_specifications' => ['nullable'],
            'salient_features' => ['nullable'],
            'utility_requirements' => ['nullable'],
            'unit' => ['nullable', 'string', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        foreach (['technical_specifications', 'input_specifications', 'salient_features', 'utility_requirements'] as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }
            $value = $data[$field];
            if (is_string($value)) {
                $value = trim($value);
                $data[$field] = $value === '' ? null : (json_decode($value, true) ?? null);
                if ($value !== '' && $data[$field] === null && json_last_error() !== JSON_ERROR_NONE) {
                    abort(response()->json([
                        'success' => false,
                        'message' => "Invalid JSON in {$field}.",
                        'errors' => [$field => ["Invalid JSON in {$field}."]],
                    ], 422));
                }
            }
        }

        return $data;
    }
}
