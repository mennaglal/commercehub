<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /* ---------------- Order status ---------------- */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_CANCELLED];

    // From which status to which statuses we can move.
    public const STATUS_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED => [self::STATUS_CANCELLED],
        self::STATUS_CANCELLED => [],
    ];

    /* ---------------- Delivery status ---------------- */
    public const DELIVERY_PENDING = 'pending';
    public const DELIVERY_PREPARING = 'preparing';
    public const DELIVERY_SHIPPED = 'shipped';
    public const DELIVERY_DELIVERED = 'delivered';
    public const DELIVERY_RETURNED = 'returned';

    public const DELIVERY_STATUSES = [
        self::DELIVERY_PENDING, self::DELIVERY_PREPARING, self::DELIVERY_SHIPPED,
        self::DELIVERY_DELIVERED, self::DELIVERY_RETURNED,
    ];

    public const DELIVERY_TRANSITIONS = [
        self::DELIVERY_PENDING => [self::DELIVERY_PREPARING, self::DELIVERY_SHIPPED],
        self::DELIVERY_PREPARING => [self::DELIVERY_SHIPPED],
        self::DELIVERY_SHIPPED => [self::DELIVERY_DELIVERED, self::DELIVERY_RETURNED],
        self::DELIVERY_DELIVERED => [self::DELIVERY_RETURNED],
        self::DELIVERY_RETURNED => [],
    ];

    /* ---------------- Payment ---------------- */
    public const PAYMENT_COD = 'cod';
    public const PAYMENT_ONLINE = 'online';
    public const PAYMENT_BANK = 'bank_transfer';

    public const PAYMENT_METHODS = [self::PAYMENT_COD, self::PAYMENT_ONLINE, self::PAYMENT_BANK];

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING, self::PAYMENT_PAID, self::PAYMENT_FAILED, self::PAYMENT_REFUNDED,
    ];

    /* ---------------- Blogger commission ---------------- */
    public const COMMISSION_PENDING = 'pending';     // order created
    public const COMMISSION_EARNED = 'earned';       // order delivered
    public const COMMISSION_CANCELLED = 'cancelled'; // order cancelled or returned

    public const COMMISSION_STATUSES = [
        self::COMMISSION_PENDING, self::COMMISSION_EARNED, self::COMMISSION_CANCELLED,
    ];

    // Bootstrap badge colors, by column name then value.
    public const BADGES = [
        'status' => ['pending' => 'warning', 'confirmed' => 'info', 'cancelled' => 'danger'],
        'delivery_status' => [
            'pending' => 'secondary', 'preparing' => 'warning', 'shipped' => 'primary',
            'delivered' => 'success', 'returned' => 'danger',
        ],
        'payment_status' => ['pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'secondary'],
        'commission_status' => ['pending' => 'warning', 'earned' => 'success', 'cancelled' => 'danger'],
    ];

    protected $fillable = [
        'order_number', 'store_id', 'status',
        'customer_name', 'customer_phone', 'shipping_address', 'notes',
        'subtotal', 'discount_total', 'shipping_total', 'total',
        'coupon_id', 'coupon_code',
        'blogger_id', 'commission_amount', 'commission_status',
        'payment_method', 'payment_gateway', 'payment_status', 'payment_id',
        'invoice_id', 'invoice_url', 'payment_data', 'paid_at',
        'delivery_status', 'tracking_number', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'total' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'payment_data' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function blogger(): BelongsTo
    {
        return $this->belongsTo(Blogger::class);
    }

    // Usage in blade: $order->badge('delivery_status')
    public function badge(string $column): string
    {
        return self::BADGES[$column][$this->{$column}] ?? 'secondary';
    }
}
