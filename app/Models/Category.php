<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'image',
        'parent_id',
    ];

    // Parent category.
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child categories.
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Category products.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
