<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use App\Models\VariantValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * ALL product validation lives here.
 * The controller trusts validated() and does not validate again.
 *
 * Variants are validated from ONE input only: variants[i][values][].
 * The option types are derived from those values, so the form does not
 * send "variant_types" / "variant_values" anymore.
 */
class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $hasVariants = $this->boolean('has_variants');

        $this->merge([
            'has_variants' => $hasVariants,
            // A product with variants has no product-level sale price.
            'is_sale' => ! $hasVariants && $this->boolean('is_sale'),
            'status' => $this->boolean('status', true),
            // Ignore any leftover variant rows when the product is simple.
            'variants' => $hasVariants ? $this->input('variants', []) : [],
        ]);
    }

    public function rules(): array
    {
        $hasVariants = $this->boolean('has_variants');
        $productId = $this->route('product'); // null when creating

        return [
            // ---- Basic data
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'description_ar' => ['nullable', 'string', 'max:20000'],
            'description_en' => ['nullable', 'string', 'max:20000'],
            'image' => [Rule::requiredIf(!$productId),'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', 'boolean'],

            // ---- Categories (the subcategory/parent match is checked in withValidator)
            'parent_category_id' => ['required', 'integer', Rule::exists('categories', 'id')->whereNull('parent_id')],
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            // ---- Simple product: price / sale / stock are required only when there are no variants
            'has_variants' => ['required', 'boolean'],
            'price' => [Rule::requiredIf(! $hasVariants), 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => [Rule::requiredIf(! $hasVariants), 'nullable', 'integer', 'min:0', 'max:2147483647'],
            'is_sale' => ['required', 'boolean'],
            'sale_price' => [
                Rule::excludeIf($hasVariants),
                Rule::requiredIf(! $hasVariants && $this->boolean('is_sale')),
                'nullable', 'numeric', 'min:0', 'max:99999999.99',
                'lte:price', // sale price cannot exceed the regular price
            ],

            // ---- Product with variants: at least one row is required
            'variants' => [Rule::excludeIf(! $hasVariants),Rule::requiredIf($hasVariants), 'array', 'max:500'],
            'variants.*.values' => ['required', 'array', 'min:1'],
            'variants.*.values.*' => ['required', 'integer', 'exists:variant_values,id'],
            'variants.*.price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'lte:variants.*.price'],
            'variants.*.quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'variants.*.sku' => [
                'nullable', 'string', 'max:255',
                'distinct', // no repeated SKU inside this form
                // product_variants.sku is unique in the DB: reject SKUs used by OTHER products
                Rule::unique('product_variants', 'sku')->where(
                    fn ($query) => $productId ? $query->where('product_id', '!=', $productId) : $query
                ),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->checkSubcategory($validator);

            if ($this->boolean('has_variants')) {
                $this->checkCombinations($validator);
            }
        });
    }

    // The chosen subcategory must belong to the chosen main category.
    private function checkSubcategory(Validator $validator): void
    {
        $parentId = $this->input('parent_category_id');
        $categoryId = $this->input('category_id');

        if ($parentId && $categoryId
            && ! Category::whereKey($categoryId)->where('parent_id', $parentId)->exists()) {
            $validator->errors()->add('category_id', __('products.errors.subcategory_mismatch'));
        }
    }

    // Rules that need to look at ALL rows together:
    //  1) every value is active
    //  2) a row has exactly one value per type (no Red + Blue together)
    //  3) every row uses the same set of types (no row with Color only and another with Color + Size)
    //  4) no duplicated combination
    private function checkCombinations(Validator $validator): void
    {
        $rows = $this->input('variants', []);

        // One query: value id => its type id (active values only).
        $typeOfValue = VariantValue::where('status', true)
            ->whereIn('id', collect($rows)->pluck('values')->flatten()->unique())
            ->pluck('variant_type_id', 'id');

        $expectedTypes = null;
        $seen = [];

        foreach ($rows as $index => $row) {
            $ids = collect($row['values'] ?? [])->map(fn ($id) => (int) $id);
            $types = $ids->map(fn ($id) => $typeOfValue->has($id) ? (int) $typeOfValue[$id] : null);

            $isValid = $types->filter()->count() === $types->count()   // all values active
                && $types->unique()->count() === $types->count();       // one value per type

            $typeSet = $types->sort()->values()->all();
            $expectedTypes ??= $typeSet;

            if (! $isValid || $typeSet !== $expectedTypes) {
                $validator->errors()->add(
                    "variants.{$index}.values",
                    __('products.errors.invalid_combination')
                );
                continue;
            }

            $key = $ids->sort()->implode('-');

            if (isset($seen[$key])) {
                $validator->errors()->add("variants.{$index}.values", __('products.errors.duplicate_combination'));
            }

            $seen[$key] = true;
        }
    }
}
