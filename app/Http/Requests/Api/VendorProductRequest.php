<?php

namespace App\Http\Requests\Api;

use App\Models\VariantValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Create (POST) and update (PUT) of a vendor product. Same rules as the admin ProductRequest, with two differences:
 *  - there is no store_id: the store is always the store of the logged-in vendor,
 *  - the category is a SUBCATEGORY (one field: category_id).
 *
 * Two kinds of products:
 *   has_variants = false : price, stock, is_sale, sale_price
 *   has_variants = true  : variants = [ { values: [value ids], price, discount_price, quantity, sku } ]
 */
class VendorProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasVariants = $this->boolean('has_variants');
        $productId = $this->route('product'); // null when creating

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'description_ar' => ['nullable', 'string', 'max:20000'],
            'description_en' => ['nullable', 'string', 'max:20000'],
            'image' => [Rule::requiredIf(! $productId), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['sometimes', 'boolean'],

            // A subcategory (a category that has a parent).
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->whereNotNull('parent_id')],

            'has_variants' => ['required', 'boolean'],
            'price' => [Rule::requiredIf(! $hasVariants), 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => [Rule::requiredIf(! $hasVariants), 'nullable', 'integer', 'min:0', 'max:2147483647'],
            'is_sale' => ['sometimes', 'boolean'],
            'sale_price' => [
                Rule::excludeIf($hasVariants),
                Rule::requiredIf(! $hasVariants && $this->boolean('is_sale')),
                'nullable', 'numeric', 'min:0', 'max:99999999.99', 'lte:price',
            ],

            'variants' => [Rule::excludeIf(! $hasVariants), Rule::requiredIf($hasVariants), 'array', 'max:500'],
            'variants.*.values' => ['required', 'array', 'min:1'],
            'variants.*.values.*' => ['required', 'integer', 'exists:variant_values,id'],
            'variants.*.price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'lte:variants.*.price'],
            'variants.*.quantity' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'variants.*.sku' => [
                'nullable', 'string', 'max:255', 'distinct',
                // product_variants.sku is unique in the DB: reject SKUs used by OTHER products.
                Rule::unique('product_variants', 'sku')->where(
                    fn ($query) => $productId ? $query->where('product_id', '!=', $productId) : $query
                ),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->boolean('has_variants')) {
                $this->checkCombinations($validator);
            }
        });
    }

    // Rules that need to look at ALL rows together:
    //  1) every value is active
    //  2) a row has exactly one value per type (no Red + Blue together)
    //  3) every row uses the same set of types
    //  4) no duplicated combination
    private function checkCombinations(Validator $validator): void
    {
        $rows = $this->input('variants', []);

        // One query: value id => its type id (active values of active types only).
        $typeOfValue = VariantValue::where('status', true)
            ->whereHas('variantType', fn ($query) => $query->where('status', true))
            ->whereIn('id', collect($rows)->pluck('values')->flatten()->unique())
            ->pluck('variant_type_id', 'id');

        $expectedTypes = null;
        $seen = [];

        foreach ($rows as $index => $row) {
            $ids = collect($row['values'] ?? [])->map(fn ($id) => (int) $id);
            $types = $ids->map(fn ($id) => $typeOfValue->has($id) ? (int) $typeOfValue[$id] : null);

            $isValid = $types->filter()->count() === $types->count()
                && $types->unique()->count() === $types->count();

            $typeSet = $types->sort()->values()->all();
            $expectedTypes ??= $typeSet;

            if (! $isValid || $typeSet !== $expectedTypes) {
                $validator->errors()->add("variants.{$index}.values", __('products.errors.invalid_combination'));
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
