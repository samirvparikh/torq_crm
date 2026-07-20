<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(['category']);
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Product::query()->with('category');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        \App\Support\DatatableSort::apply($query, $filters, [
            'id', 'name', 'sku', 'category_id', 'price', 'tax_rate', 'is_active', 'created_at',
        ], 'id', 'desc');

        return $query->paginate($perPage);
    }
}
