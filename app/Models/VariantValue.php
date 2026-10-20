<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantValue extends Model
{
    protected $fillable = [
        'variant_type_id',
        'value_ar',
        'value_en',
        'color_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }
}
