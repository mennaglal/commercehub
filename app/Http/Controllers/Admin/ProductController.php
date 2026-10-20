<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with([
            'store',
            'category.parent',
            'variants',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.products.index', compact('products'));
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();

        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $categories = Category::whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        $variantTypes = VariantType::with([
            'values' => fn ($query) => $query
                ->where('status', true)
                ->orderBy('id'),
        ])
            ->where('status', true)
            ->orderBy('name_en')
            ->get();

        return view('admin.pages.products.create', compact(
            'stores',
            'parentCategories',
            'categories',
            'variantTypes'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        $imagePath = null;

        try {
            DB::transaction(function () use ($request, &$data, &$imagePath) {
                $data['slug'] = $this->makeUniqueSlug(
                    $data['slug'] ?? $data['name_en']
                );

                $data['name'] = $data['name_en'];
                $data['description'] = $data['description_en'] ?? null;

                if (!empty($data['variants'])) {
                    $data['stock'] = collect($data['variants'])->sum('quantity');
                }

                if ($request->hasFile('image')) {
                    $imagePath = $this->uploadImage($request->file('image'));
                }

                $data['image'] = $imagePath;
                $data['status'] = $request->boolean('status');

                $product = Product::create(
                    $this->productAttributes($data)
                );

                $this->syncVariants($product, $data);
            });
        } catch (\Throwable $e) {
            if ($imagePath) {
                $this->deleteImage($imagePath);
            }

            throw $e;
        }

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
        $product = Product::with([
            'category.parent',
            'variants.values',
            'variantTypes.values',
        ])->findOrFail($id);

        $stores = Store::orderBy('name')->get();

        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $categories = Category::whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        $variantTypes = VariantType::with([
            'values' => fn ($query) => $query
                ->where('status', true)
                ->orderBy('id'),
        ])
            ->where('status', true)
            ->orderBy('name_en')
            ->get();

        return view('admin.pages.products.edit', compact(
            'product',
            'stores',
            'parentCategories',
            'categories',
            'variantTypes'
        ));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $data = $this->validateProduct($request, $product);

        $oldImage = $product->image;
        $newImage = null;

        try {
            DB::transaction(function () use (
                $request,
                $product,
                &$data,
                &$newImage
            ) {
                $data['slug'] = $this->makeUniqueSlug(
                    $data['slug'] ?? $data['name_en'],
                    $product->id
                );

                $data['name'] = $data['name_en'];
                $data['description'] = $data['description_en'] ?? null;

                if (!empty($data['variants'])) {
                    $data['stock'] = collect($data['variants'])->sum('quantity');
                }

                if ($request->hasFile('image')) {
                    $newImage = $this->uploadImage($request->file('image'));
                    $data['image'] = $newImage;
                }

                $data['status'] = $request->boolean('status');

                $product->update($this->productAttributes($data));

                $this->syncVariants($product, $data);
            });
        } catch (\Throwable $e) {
            if ($newImage) {
                $this->deleteImage($newImage);
            }

            throw $e;
        }

        if ($newImage && $oldImage) {
            $this->deleteImage($oldImage);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $image = $product->image;

        try {
            $product->delete();
        } catch (QueryException $e) {
            return back()->with('error', __('messages.delete_failed'));
        }

        if ($image) {
            $this->deleteImage($image);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', __('messages.deleted_successfully'));
    }

    private function validateProduct(
        Request $request,
        ?Product $product = null
    ): array {
        $data = $request->validate([
            'store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],

            'parent_category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->whereNull('parent_id'),
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product?->id),
            ],

            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            'price' => ['required', 'numeric', 'min:0'],

            'is_sale' => ['nullable', 'boolean'],

            'sale_price' => [
                'nullable',
                'required_if:is_sale,1',
                'numeric',
                'min:0',
            ],

            'stock' => ['required', 'integer', 'min:0'],

            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'status' => ['nullable', 'boolean'],

            'variant_types' => ['nullable', 'array'],
            'variant_types.*' => [
                'required',
                'integer',
                'distinct',
                'exists:variant_types,id',
            ],

            'variant_values' => ['nullable', 'array'],
            'variant_values.*' => ['array'],
            'variant_values.*.*' => [
                'required',
                'integer',
                'distinct',
                'exists:variant_values,id',
            ],

            'variants' => ['nullable', 'array'],
            'variants.*.values' => [
                'required',
                'array',
                'min:1',
            ],
            'variants.*.values.*' => [
                'required',
                'integer',
                'distinct',
                'exists:variant_values,id',
            ],
            'variants.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'variants.*.discount_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'variants.*.quantity' => [
                'required',
                'integer',
                'min:0',
            ],
            'variants.*.sku' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $subcategoryBelongsToParent = Category::whereKey($data['category_id'])
            ->where('parent_id', $data['parent_category_id'])
            ->exists();

        if (!$subcategoryBelongsToParent) {
            throw ValidationException::withMessages([
                'category_id' => 'The selected subcategory does not belong to the selected main category.',
            ]);
        }

        $data['is_sale'] = $request->boolean('is_sale');

        if ($data['is_sale']) {
            if ((float) $data['sale_price'] > (float) $data['price']) {
                throw ValidationException::withMessages([
                    'sale_price' => 'Sale price cannot exceed the regular price.',
                ]);
            }
        } else {
            $data['sale_price'] = null;
        }

        $data['variant_types'] = $data['variant_types'] ?? [];
        $data['variant_values'] = $data['variant_values'] ?? [];
        $data['variants'] = $data['variants'] ?? [];

        return $data;
    }

    private function productAttributes(array $data): array
    {
        unset(
            $data['parent_category_id'],
            $data['variant_types'],
            $data['variant_values'],
            $data['variants']
        );

        return $data;
    }

    private function syncVariants(Product $product, array $data): void
    {
        $typeIds = collect($data['variant_types'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $selectedValueIds = collect($data['variant_values'])
            ->flatten()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($typeIds->isEmpty()) {
            if (count($data['variants']) > 0) {
                throw ValidationException::withMessages([
                    'variant_types' => 'Select at least one variant type.',
                ]);
            }

            $product->variants()->delete();
            $product->variantTypes()->detach();

            return;
        }

        $actualTypeIds = VariantType::whereIn('id', $typeIds)
            ->where('status', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($actualTypeIds->count() !== $typeIds->count()) {
            throw ValidationException::withMessages([
                'variant_types' => 'One or more selected variant types are invalid.',
            ]);
        }

        $values = VariantValue::whereIn('id', $selectedValueIds)
            ->where('status', true)
            ->get();

        if ($values->count() !== $selectedValueIds->count()) {
            throw ValidationException::withMessages([
                'variant_values' => 'One or more selected values are invalid.',
            ]);
        }

        $valuesByType = $values->groupBy('variant_type_id');

        foreach ($typeIds as $typeId) {
            if ($valuesByType->get($typeId, collect())->isEmpty()) {
                throw ValidationException::withMessages([
                    'variant_values' => 'Select values for every selected variant type.',
                ]);
            }
        }

        foreach ($values as $value) {
            if (!$typeIds->contains((int) $value->variant_type_id)) {
                throw ValidationException::withMessages([
                    'variant_values' => 'A selected value does not belong to the selected types.',
                ]);
            }
        }

        $product->variantTypes()->sync($typeIds->all());

        $seenCombinations = [];

        foreach ($data['variants'] as $index => $variantData) {
            $variantValueIds = collect($variantData['values'])
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            $combinationValues = $values->whereIn('id', $variantValueIds);

            if (
                $variantValueIds->count() !== $typeIds->count()
                || $combinationValues->count() !== $typeIds->count()
                || $combinationValues->pluck('variant_type_id')->unique()->count() !== $typeIds->count()
                || $typeIds->diff(
                    $combinationValues->pluck('variant_type_id')
                        ->map(fn ($id) => (int) $id)
                )->isNotEmpty()
            ) {
                throw ValidationException::withMessages([
                    "variants.{$index}.values" => 'Each combination must contain exactly one value from every selected type.',
                ]);
            }

            $combinationKey = $variantValueIds->implode('-');

            if (isset($seenCombinations[$combinationKey])) {
                throw ValidationException::withMessages([
                    "variants.{$index}.values" => 'Duplicate variant combinations are not allowed.',
                ]);
            }

            $seenCombinations[$combinationKey] = true;

            $discount = $variantData['discount_price'] ?? null;

            if ($discount !== null && (float) $discount > (float) $variantData['price']) {
                throw ValidationException::withMessages([
                    "variants.{$index}.discount_price" => 'Discount price cannot exceed the variant price.',
                ]);
            }
        }

        $existingVariants = $product->variants()
            ->with('values')
            ->get();

        $existingByCombination = [];

        foreach ($existingVariants as $existingVariant) {
            $key = $existingVariant->values
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->implode('-');

            $existingByCombination[$key] = $existingVariant;
        }

        $keptIds = [];

        foreach ($data['variants'] as $variantData) {
            $key = collect($variantData['values'])
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->implode('-');

            $variant = $existingByCombination[$key] ?? new ProductVariant();

            $variant->fill([
                'sku' => $variantData['sku'] ?? null,
                'price' => $variantData['price'],
                'discount_price' => $variantData['discount_price'] ?? null,
                'quantity' => $variantData['quantity'],
                'status' => true,
            ]);

            $product->variants()->save($variant);

            $variant->values()->sync($variantData['values']);

            $keptIds[] = $variant->id;
        }

        if (!empty($keptIds)) {
            $product->variants()
                ->whereNotIn('id', $keptIds)
                ->delete();
        } else {
            $product->variants()->delete();
        }
    }

    private function makeUniqueSlug(
        string $value,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($value);

        if ($base === '') {
            $base = 'product';
        }

        $slug = $base;
        $counter = 1;

        while (
        Product::where('slug', $slug)
            ->when(
                $ignoreId,
                fn ($query) => $query->where('id', '!=', $ignoreId)
            )
            ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function uploadImage($file): string
    {
        $directory = public_path('uploads/products');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->extension();

        $file->move($directory, $filename);

        return 'uploads/products/' . $filename;
    }

    private function deleteImage(string $path): void
    {
        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
