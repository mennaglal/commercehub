<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantType extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'input_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }


    public function values()
    {
        return $this->hasMany(VariantValue::class);
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_variant_types'
        )->withTimestamps();
    }

}
