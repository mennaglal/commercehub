<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

/**
 * The controller only SAVES data. All validation is in ProductRequest.
 *
 * Two kinds of products:
 *  - simple product (has_variants = false): price / stock are on the product.
 *  - product with variants (has_variants = true): price / stock are on the variants,
 *    and the product keeps the lowest price and the total stock for listings.
 */
class ProductController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:products.view', only: ['index', 'show']),
            new Middleware('permission:products.create', only: ['create', 'store']),
            new Middleware('permission:products.update', only: ['edit', 'update']),
            new Middleware('permission:products.delete', only: ['destroy', 'bulkDestroy']),
        ];
    }


    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        // Product statistics (all products, independent of filters).
        $statistics = Product::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock')
            ->selectRaw(
                'SUM(CASE WHEN stock > 0 AND stock <= ? THEN 1 ELSE 0 END) as low_stock',
                [Product::LOW_STOCK_THRESHOLD]
            )
            ->selectRaw('SUM(CASE WHEN has_variants = 1 THEN 1 ELSE 0 END) as variable')
            ->first();

        // Products query.
        $query = Product::with(['store', 'category.parent'])
            ->withCount('variants');

        // Search.
        $query->when($search !== '', function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        });

        // Product status.
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status === 'active');
        });

        // Stock status.
        $query->when($request->filled('stock_status'), function ($q) use ($request) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $q->where('stock', '>', Product::LOW_STOCK_THRESHOLD);
                    break;

                case 'low_stock':
                    $q->where('stock', '>', 0)
                        ->where('stock', '<=', Product::LOW_STOCK_THRESHOLD);
                    break;

                case 'out_of_stock':
                    $q->where('stock', '<=', 0);
                    break;
            }
        });

        // Product type.
        $query->when($request->filled('type'), function ($q) use ($request) {
            $q->where('has_variants', $request->type === 'variable');
        });

        // Sale status.
        $query->when($request->filled('sale'), function ($q) use ($request) {
            $q->where('is_sale', $request->sale === 'yes');
        });

        $products = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.products.index', compact(
            'products',
            'statistics'
        ));
    }


    public function create()
    {
        return view('admin.pages.products.create', $this->formData());
    }

    public function store(ProductRequest $request)
    {
        $this->save($request, new Product());

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function show(string $id)
    {
        return redirect()->route('admin.products.edit', $id);
    }

    public function edit(string $id)
    {
        $product = Product::with(['category.parent', 'variants.values'])->findOrFail($id);

        return view('admin.pages.products.edit', $this->formData() + ['product' => $product]);
    }

    public function update(ProductRequest $request, string $id)
    {
        $this->save($request, Product::findOrFail($id));

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(string $id)
    {
        if (! $this->deleteProduct(Product::findOrFail($id))) {
            return back()->with('error', __('products.delete_failed'));
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('messages.deleted_successfully'));
    }

    // Delete the products ticked in the list (ids[]).
    // A product that cannot be deleted (for example it is used by an order) is skipped, not fatal.
    public function bulkDestroy(Request $request)
    {
        $ids = collect((array) $request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(200); // safety limit per request

        // Keep the search and page size when going back to the list.
        $listParams = $request->only(['search', 'per_page']);

        if ($ids->isEmpty()) {
            return redirect()
                ->route('admin.products.index', $listParams)
                ->with('error', __('products.bulk.nothing_selected'));
        }

        $deleted = 0;

        foreach (Product::whereIn('id', $ids)->get() as $product) {
            if ($this->deleteProduct($product)) {
                $deleted++;
            }
        }

        return redirect()
            ->route('admin.products.index', $listParams)
            ->with($deleted > 0 ? 'success' : 'error', __('products.bulk.result', [
                'deleted' => $deleted,
                'skipped' => $ids->count() - $deleted,
            ]));
    }

    /* -----------------------------------------------------------------
     | Helpers
     |-----------------------------------------------------------------*/

    // Delete one product and its image. Returns false when the DB refuses (product in use).
    private function deleteProduct(Product $product): bool
    {
        $image = $product->image;

        try {
            $product->delete(); // variants are removed by the DB cascade
        } catch (QueryException $e) {
            return false;
        }

        if ($image) {
            $this->deleteImage($image);
        }

        return true;
    }

    // Lists needed by the create and edit forms (one place instead of two copies).
    private function formData(): array
    {
        return [
            'stores' => Store::orderBy('name')->get(),
            'parentCategories' => Category::whereNull('parent_id')->orderBy('name')->get(),
            'categories' => Category::whereNotNull('parent_id')->orderBy('name')->get(),
            // Only active types and values can be chosen for a product.
            'variantTypes' => VariantType::with([
                'values' => fn ($query) => $query->where('status', true)->orderBy('id'),
            ])
                ->where('status', true)
                ->orderBy('name_en')
                ->get(),
        ];
    }

    // One method for create AND update: the only difference is the Product instance.
    private function save(ProductRequest $request, Product $product): void
    {
        $data = $request->validated();
        $hasVariants = $data['has_variants'];

        $oldImage = $product->image;
        $newImage = null;

        try {
            DB::transaction(function () use ($request, $product, $data, $hasVariants, &$newImage) {
                // 1) Derived fields
                $data['name'] = $data['name_en'];
                $data['description'] = $data['description_en'] ?? null;
                $data['slug'] = $this->makeUniqueSlug($data['slug'] ?? $data['name_en'], $product->id);

                // validated() holds an UploadedFile here: replace it with the stored path,
                // or drop it so the current image stays untouched.
                unset($data['image']);

                if ($request->hasFile('image')) {
                    $newImage = $this->uploadImage($request->file('image'));
                    $data['image'] = $newImage;
                }

                // A product with variants gets these values from its variants (step 3 below).
                if ($hasVariants) {
                    $data['price'] = 0;
                    $data['stock'] = 0;
                    $data['sale_price'] = null;
                }

                // 2) Save the product (Arr::only keeps only the real columns)
                $product->fill(Arr::only($data, $product->getFillable()))->save();

                // 3) Save the variants
                $this->syncVariants($product, $hasVariants ? $data['variants'] : []);

                // 4) Keep the lowest price and total stock on the product for listings
                if ($hasVariants) {
                    $product->update([
                        'price' => $product->variants()->min('price'),
                        'stock' => $product->variants()->sum('quantity'),
                    ]);
                }
            });
        } catch (Throwable $e) {
            // The DB rolled back, so remove the image we just uploaded.
            if ($newImage) {
                $this->deleteImage($newImage);
            }

            throw $e;
        }

        // Only after a successful save: remove the replaced image.
        if ($newImage && $oldImage) {
            $this->deleteImage($oldImage);
        }
    }

    // Make the DB match the rows sent by the form.
    // $rows = [] means a simple product.
    private function syncVariants(Product $product, array $rows): void
    {
        if (empty($rows)) {
            // NOTE: this is a hard delete. When orders start referencing variants,
            // switch to SoftDeletes so old orders keep their history.
            $product->variants()->delete();
            $product->variantTypes()->detach();

            return;
        }

        // The option types come from the chosen values
        // (the request already checked that every row uses the same types).
        $valueIds = collect($rows)->pluck('values')->flatten()->unique();
        $product->variantTypes()->sync(
            VariantValue::whereIn('id', $valueIds)->pluck('variant_type_id')->unique()
        );

        // Existing variants indexed by their combination ("3-7" = value ids 3 and 7),
        // so editing a row updates it instead of deleting and recreating it.
        $existing = $product->variants()->with('values')->get()
            ->keyBy(fn ($variant) => $this->combinationKey($variant->values->pluck('id')));

        $keptIds = [];

        foreach ($rows as $row) {
            $variant = $existing->get($this->combinationKey($row['values'])) ?? new ProductVariant();

            $variant->fill(Arr::only($row, ['sku', 'price', 'discount_price', 'quantity']));
            $product->variants()->save($variant); // sets product_id and saves

            $variant->values()->sync($row['values']);

            $keptIds[] = $variant->id;
        }

        // Variants that are no longer in the form are removed.
        $product->variants()->whereNotIn('id', $keptIds)->delete();
    }

    // [7, 3] and [3, 7] must give the same key: "3-7".
    private function combinationKey(iterable $valueIds): string
    {
        return collect($valueIds)->map(fn ($id) => (int) $id)->sort()->implode('-');
    }

    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'product';
        $slug = $base;
        $counter = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function uploadImage($file): string
    {
        $directory = public_path('uploads/products');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->extension();
        $file->move($directory, $filename);

        return 'uploads/products/' . $filename;
    }

    private function deleteImage(string $path): void
    {
        // Safety: only delete files inside uploads/products.
        if (! str_starts_with($path, 'uploads/products/') || str_contains($path, '..')) {
            return;
        }

        $fullPath = public_path($path);

        if (is_file($fullPath)) {
            File::delete($fullPath);
        }
    }
}
