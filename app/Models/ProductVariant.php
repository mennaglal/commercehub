<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    // "status" is not fillable on purpose: the admin form does not edit it,
    // so a new variant gets the DB default (active) and an existing one keeps its value.
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'discount_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'quantity' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // The option values of this variant, e.g. [Red, M].
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(VariantValue::class, 'product_variant_values')
            ->withTimestamps();
    }

    // Price the customer pays for this variant.
    public function getFinalPriceAttribute(): string
    {
        return $this->discount_price ?? $this->price;
    }
}
