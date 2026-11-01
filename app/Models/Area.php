<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'city_id',
        'shipping_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'shipping_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return (app()->getLocale() == "ar")
            ? $this->name_ar
            : $this->name_en;
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
