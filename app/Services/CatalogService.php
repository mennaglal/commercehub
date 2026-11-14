<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * What the customers can see. One rule for everything:
 *   a product is visible only if the product is active AND its store is active AND approved.
 */
class CatalogService
{
    // The price the customer really pays (sale price when the product is on sale).
    private const FINAL_PRICE = 'CASE WHEN products.is_sale = 1 AND products.sale_price IS NOT NULL THEN products.sale_price ELSE products.price END';

    /* ---------------- Categories ---------------- */

    // Main categories with their subcategories: 2 queries.
    public function categories(): Collection
    {
        return Category::whereNull('parent_id')->with('children')->orderBy('id')->get();
    }

    /* ---------------- Products ---------------- */

    public function visibleProducts(): Builder
    {
        return Product::query()
            ->where('products.status', true)
            ->where(function ($query) {
                // Platform Products: products without a store.
                $query->whereNull('products.store_id')

                    // Vendor Products: products belonging to an active, approved store.
                    ->orWhereHas('store', fn ($store) => $store
                        ->where('status', true)
                        ->where('approval_status', 'approved')
                    );
            });

    }

    public function products(array $filters): LengthAwarePaginator
    {
        $query = $this->visibleProducts()->with(['category:id,name,name_ar,slug,parent_id', 'store:id,name,slug']);

        if (! empty($filters['q'])) {
            // % and _ typed by the user are searched as normal characters.
            $term = '%' . addcslashes($filters['q'], '%_\\') . '%';

            $query->where(function ($query) use ($term) {
                $query->where('products.name_ar', 'like', $term)
                    ->orWhere('products.name_en', 'like', $term)
                    ->orWhere('products.sku', 'like', $term);
            });
        }

        if (! empty($filters['category_id'])) {
            // A main category also shows the products of its subcategories.
            $ids = Category::where('id', $filters['category_id'])->orWhere('parent_id', $filters['category_id'])->pluck('id');

            $query->whereIn('products.category_id', $ids);
        }

        if (! empty($filters['store_id'])) {
            $query->where('products.store_id', $filters['store_id']);
        }

        if (isset($filters['min_price'])) {
            $query->whereRaw('(' . self::FINAL_PRICE . ') >= ?', [$filters['min_price']]);
        }

        if (isset($filters['max_price'])) {
            $query->whereRaw('(' . self::FINAL_PRICE . ') <= ?', [$filters['max_price']]);
        }

        if (! empty($filters['on_sale'])) {
            $query->where('products.is_sale', true);
        }

        if (! empty($filters['in_stock'])) {
            $query->where('products.stock', '>', 0);
        }

        match ($filters['sort'] ?? 'newest') {
            'price_asc' => $query->orderByRaw(self::FINAL_PRICE . ' asc'),
            'price_desc' => $query->orderByRaw(self::FINAL_PRICE . ' desc'),
            'name' => $query->orderBy(app()->getLocale() === 'ar' ? 'products.name_ar' : 'products.name_en'),
            default => $query->latest('products.id'),
        };

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    // The product page: only the active variants, with their values and the type of each value.
    public function productById(int $id): Product
    {
        return $this->visibleProducts()
            ->where('products.id', $id)
            ->with([
                'category.parent',
                'store.country',
                'store.city',
                'store.area',
                'variants' => fn ($variants) => $variants
                    ->where('status', true)
                    ->with('values.variantType'),
            ])
            ->firstOrFail();
    }

    /* ---------------- Stores ---------------- */

    public function stores(?string $search, int $perPage): LengthAwarePaginator
    {
        return $this->visibleStores()
            ->when($search, fn ($query) => $query->where('name', 'like', '%' . addcslashes($search, '%_\\') . '%'))
            ->withCount(['products' => fn ($products) => $products->where('status', true)])
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function storeById(int $id): Store
    {
        return $this->visibleStores()
            ->where('stores.id', $id)
            ->with(['country', 'city', 'area'])
            ->withCount([
                'products' => fn ($products) => $products->where('status', true)
            ])
            ->firstOrFail();
    }

    private function visibleStores(): Builder
    {
        return Store::query()->where('status', true)->where('approval_status', 'approved');
    }
}
