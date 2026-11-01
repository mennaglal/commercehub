<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// An option type such as "Color" or "Size". Managed from the variant-types admin pages.
class VariantType extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'input_type', // 'text' or 'color'
        'status',
    ];

    protected function casts(): array
    {
        return [
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

    // The values that belong to this type, e.g. Red / Blue for Color.
    public function values(): HasMany
    {
        return $this->hasMany(VariantValue::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_variant_types')
            ->withTimestamps();
    }
}
