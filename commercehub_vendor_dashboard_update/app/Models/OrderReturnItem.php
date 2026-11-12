<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturnItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_return_id', 'order_item_id',
        'quantity', 'refund_amount', 'commission_amount', 'restock',
        'vendor_status', 'vendor_note', 'vendor_processed_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refund_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'restock' => 'boolean',
        'vendor_processed_at' => 'datetime',
    ];

    public function orderReturn(): BelongsTo
    {
        return $this->belongsTo(OrderReturn::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
