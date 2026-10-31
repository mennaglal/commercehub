<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// One option value such as "Red" or "XL". Always belongs to a VariantType.
class VariantValue extends Model
{
    protected $fillable = [
        'variant_type_id',
        'value_ar',
        'value_en',
        'color_code', // only used when the type input_type is 'color'
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
    protected $appends = ['value'];

    public function getValueAttribute(): string
    {
        return (app()->getLocale() == "ar")
            ? $this->value_ar
            : $this->value_en;
    }

    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }

    // The product variants that use this value.
    public function productVariants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values')
            ->withTimestamps();
    }
}
