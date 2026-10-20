<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'name_ar',
        'name_en',
        'slug',
        'description',
        'description_ar',
        'description_en',
        'price',
        'sale_price',
        'is_sale',
        'stock',
        'sku',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'is_sale' => 'boolean',
            'stock' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // The product belongs to its subcategory.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function variantTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            VariantType::class,
            'product_variant_types'
        )->withTimestamps();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    // Return the main category through the product subcategory.
    public function getParentCategoryAttribute(): ?Category
    {
        return $this->category?->parent;
    }

    // Return the effective product price.
    public function getFinalPriceAttribute(): string
    {
        if ($this->is_sale && $this->sale_price !== null) {
            return $this->sale_price;
        }

        return $this->price;
    }
}
