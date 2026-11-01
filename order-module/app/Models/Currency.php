<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'symbol',
        'decimal_places',
        'rate_to_egp',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate_to_egp' => 'decimal:8',
            'decimal_places' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }
}
