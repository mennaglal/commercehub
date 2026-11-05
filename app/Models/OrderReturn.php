<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    /* ---------------- Return status ---------------- */
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        self::STATUS_REQUESTED,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_RECEIVED,
        self::STATUS_REFUNDED,
    ];

    // From which status to which statuses we can move (same idea as the order transitions).
    public const TRANSITIONS = [
        self::STATUS_REQUESTED => [self::STATUS_APPROVED, self::STATUS_REJECTED],
        self::STATUS_APPROVED => [self::STATUS_RECEIVED, self::STATUS_REJECTED],
        self::STATUS_RECEIVED => [self::STATUS_REFUNDED],
        self::STATUS_REJECTED => [],
        self::STATUS_REFUNDED => [],
    ];

    /* ---------------- Reasons ---------------- */
    public const REASONS = [
        'defective',
        'wrong_item',
        'not_as_described',
        'changed_mind',
        'other',
    ];

    public const BADGES = [
        self::STATUS_REQUESTED => 'warning',
        self::STATUS_APPROVED => 'info',
        self::STATUS_REJECTED => 'danger',
        self::STATUS_RECEIVED => 'primary',
        self::STATUS_REFUNDED => 'success',
    ];

    protected $fillable = [
        'order_id', 'return_number', 'status', 'reason',
        'customer_note', 'admin_note',
        'refund_amount', 'refund_reference', 'processed_by',
        'approved_at', 'received_at', 'refunded_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderReturnItem::class);
    }

    public function badge(): string
    {
        return self::BADGES[$this->status] ?? 'secondary';
    }
}
