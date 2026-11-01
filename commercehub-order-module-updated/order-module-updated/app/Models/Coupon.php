<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    public const FIXED = 'fixed';
    public const PERCENTAGE = 'percentage';

    public const TYPES = [self::FIXED, self::PERCENTAGE];

    // "used_count" is not fillable on purpose: only the order flow changes it.
    protected $fillable = [
        'code', 'type', 'value', 'max_discount', 'min_order_amount',
        'usage_limit', 'starts_at', 'expires_at', 'status',
        'blogger_id', 'commission_type', 'commission_value',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function blogger(): BelongsTo
    {
        return $this->belongsTo(Blogger::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
