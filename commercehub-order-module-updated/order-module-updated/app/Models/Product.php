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
        'has_variants',
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
            'has_variants' => 'boolean',
            'status' => 'boolean',
        ];
    }

    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return (app()->getLocale() == "ar")
            ? $this->name_ar
            : $this->name_en;
    }
    /* -----------------------------------------------------------------
     | Relations
     |-----------------------------------------------------------------*/

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // The product belongs to its subcategory (the main category is category->parent).
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Only used when has_variants = true.
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // The option types (Color, Size...) this product uses.
    // They are derived from the variants and synced by ProductController.
    public function variantTypes(): BelongsToMany
    {
        return $this->belongsToMany(VariantType::class, 'product_variant_types')
            ->withTimestamps();
    }

    /* -----------------------------------------------------------------
     | Accessors
     |-----------------------------------------------------------------*/

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    public function getParentCategoryAttribute(): ?Category
    {
        return $this->category?->parent;
    }

    // Price the customer pays for a SIMPLE product.
    // For products with variants use ProductVariant::final_price instead.
    public function getFinalPriceAttribute(): string
    {
        if ($this->is_sale && $this->sale_price !== null) {
            return $this->sale_price;
        }

        return $this->price;
    }
}
