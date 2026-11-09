<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\VariantValue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * The products of ONE vendor store. Same logic as the admin ProductController::save(), for the API:
 *  - simple product (has_variants = false): price / stock are on the product.
 *  - product with variants (has_variants = true): price / stock are on the variants,
 *    and the product keeps the lowest price and the total stock for the lists.
 *
 * SECURITY: every query starts from the store of the logged-in vendor, so a vendor can never
 * read, edit or delete the product of another store (it is a 404 for him).
 */
class VendorProductService
{
    public function __construct(private ImageUploader $images)
    {
    }

    public function paginate(Store $store, ?string $search, int $perPage): LengthAwarePaginator
    {
        return $store->products()
            ->when($search, function ($query) use ($search) {
                $term = '%' . addcslashes($search, '%_\\') . '%';

                $query->where(fn ($q) => $q->where('name_ar', 'like', $term)->orWhere('name_en', 'like', $term)->orWhere('sku', 'like', $term));
            })
            ->latest('id')
            ->paginate($perPage);
    }

    public function find(Store $store, int|string $id): Product
    {
        return $store->products()->with('variants.values')->findOrFail($id);
    }

    public function create(Store $store, array $data, ?UploadedFile $image): Product
    {
        return $this->save($store->products()->make(), $store, $data, $image);
    }

    public function update(Store $store, int|string $id, array $data, ?UploadedFile $image): Product
    {
        return $this->save($store->products()->findOrFail($id), $store, $data, $image);
    }

    public function toggleStatus(Store $store, int|string $id): Product
    {
        $product = $store->products()->findOrFail($id);

        $product->update(['status' => ! $product->status]);

        return $product;
    }

    // Returns false when the database refuses (the product is in use).
    public function delete(Store $store, int|string $id): bool
    {
        $product = $store->products()->findOrFail($id);
        $image = $product->image;

        try {
            $product->delete(); // the variants are removed by the DB cascade; old orders keep their own copy
        } catch (QueryException $e) {
            return false;
        }

        $this->images->delete($image);

        return true;
    }

    /* ---------------- Save ---------------- */

    private function save(Product $product, Store $store, array $data, ?UploadedFile $image): Product
    {
        $hasVariants = (bool) $data['has_variants'];
        $oldImage = $product->image;
        $newImage = null;

        try {
            DB::transaction(function () use ($product, $store, $data, $image, $hasVariants, &$newImage) {
                // 1) Derived fields
                $data['store_id'] = $store->id;                       // always the store of the vendor
                $data['name'] = $data['name_en'];
                $data['description'] = $data['description_en'] ?? null;
                $data['slug'] = $product->exists ? $product->slug : $this->makeUniqueSlug($data['name_en']);
                $data['status'] = $data['status'] ?? ($product->exists ? $product->status : true);
                $data['is_sale'] = $hasVariants ? false : (bool) ($data['is_sale'] ?? false);

                if ($image) {
                    $newImage = $this->images->upload($image, 'products');
                    $data['image'] = $newImage;
                }

                // A product with variants gets these values from its variants (step 3).
                if ($hasVariants) {
                    $data['price'] = 0;
                    $data['stock'] = 0;
                    $data['sale_price'] = null;
                }

                // 2) Save the product (only the real columns)
                $product->fill(Arr::only($data, $product->getFillable()))->save();

                // 3) Save the variants
                $this->syncVariants($product, $hasVariants ? $data['variants'] : []);

                // 4) The lowest price and the total stock stay on the product for the lists
                if ($hasVariants) {
                    $product->update([
                        'price' => $product->variants()->min('price'),
                        'stock' => $product->variants()->sum('quantity'),
                    ]);
                }
            });
        } catch (Throwable $e) {
            // The DB rolled back, so remove the image we just uploaded.
            $this->images->delete($newImage);

            throw $e;
        }

        // Only after a successful save: remove the replaced image.
        if ($newImage) {
            $this->images->delete($oldImage);
        }

        return $product->load('variants.values');
    }

    // Make the DB match the rows sent by the app. $rows = [] means a simple product.
    private function syncVariants(Product $product, array $rows): void
    {
        if (empty($rows)) {
            $product->variants()->delete();
            $product->variantTypes()->detach();

            return;
        }

        // The option types come from the chosen values (the request checked that every row uses the same types).
        $valueIds = collect($rows)->pluck('values')->flatten()->unique();
        $product->variantTypes()->sync(VariantValue::whereIn('id', $valueIds)->pluck('variant_type_id')->unique());

        // Existing variants indexed by their combination ("3-7"), so editing a row updates it instead of recreating it.
        $existing = $product->variants()->with('values')->get()
            ->keyBy(fn ($variant) => $this->combinationKey($variant->values->pluck('id')));

        $keptIds = [];

        foreach ($rows as $row) {
            $variant = $existing->get($this->combinationKey($row['values'])) ?? new ProductVariant();

            $variant->fill(Arr::only($row, ['sku', 'price', 'discount_price', 'quantity']));
            $product->variants()->save($variant);
            $variant->values()->sync($row['values']);

            $keptIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();
    }

    // [7, 3] and [3, 7] give the same key: "3-7".
    private function combinationKey(iterable $valueIds): string
    {
        return collect($valueIds)->map(fn ($id) => (int) $id)->sort()->implode('-');
    }

    private function makeUniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'product';
        $slug = $base;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
