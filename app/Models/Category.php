<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'image',
        'parent_id',
    ];
    protected $appends = ['name_lang'];

    public function getNameLangAttribute(): string
    {
        return (app()->getLocale() == "ar")
            ? $this->name_ar
            : $this->name;
    }
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
