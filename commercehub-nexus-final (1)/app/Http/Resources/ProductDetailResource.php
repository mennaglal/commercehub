<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * The product page: the card + description + the options (Color, Size...) and the variants to choose from.
 * The controller loads: category.parent, store, variants.values.variantType (active variants only).
 */
class ProductDetailResource extends ProductResource
{
    public function toArray(Request $request): array
    {
        $isArabic = app()->getLocale() === 'ar';

        $description = $isArabic
            ? ($this->description_ar ?: $this->description_en)
            : ($this->description_en ?: $this->description_ar);

        return parent::toArray($request) + [
            'description' => $description,
            'sku' => $this->sku,
            'stock' => $this->has_variants ? null : (int) $this->stock,

            'parent_category' => $this->whenLoaded('category', fn () => $this->category?->parent
                ? new CategoryResource($this->category->parent)
                : null),

            'store_details' => $this->whenLoaded('store', fn () => new StoreResource($this->store)),

            // What the customer chooses: [{ type, input_type, values: [{ id, value, color_code }] }]
            'options' => $this->when($this->has_variants, fn () => $this->options()),

            // Every combination: its own price and stock. The app sends variant_id when ordering.
            'variants' => $this->when($this->has_variants, fn () => $this->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'discount_price' => $variant->discount_price !== null ? (float) $variant->discount_price : null,
                'final_price' => (float) $variant->final_price,
                'stock' => (int) $variant->quantity,
                'in_stock' => $variant->quantity > 0,
                'values' => $variant->values->map(fn ($value) => [
                    'id' => $value->id,
                    'type_id' => $value->variant_type_id,
                    'value' => $value->value,
                    'color_code' => $value->color_code,
                ])->values(),
            ])->values()),
        ];
    }

    // The option types with only the values that exist in an active variant.
    private function options(): array
    {
        return $this->variants
            ->flatMap(fn ($variant) => $variant->values)
            ->unique('id')
            ->groupBy('variant_type_id')
            ->map(fn ($values) => [
                'type_id' => $values->first()->variant_type_id,
                'type' => $values->first()->variantType?->name,
                'input_type' => $values->first()->variantType?->input_type,
                'values' => $values->map(fn ($value) => [
                    'id' => $value->id,
                    'value' => $value->value,
                    'color_code' => $value->color_code,
                ])->values(),
            ])
            ->values()
            ->all();
    }
}
