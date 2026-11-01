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

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CANCELLED,
    ];

    public const STATUS_TRANSITIONS = [
        self::STATUS_PENDING => [
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
        ],
        self::STATUS_CONFIRMED => [
            self::STATUS_CANCELLED,
        ],
        self::STATUS_CANCELLED => [],
    ];

    /* ---------------- Delivery status ---------------- */

    public const DELIVERY_PENDING = 'pending';
    public const DELIVERY_PREPARING = 'preparing';
    public const DELIVERY_SHIPPED = 'shipped';
    public const DELIVERY_DELIVERED = 'delivered';
    public const DELIVERY_RETURNED = 'returned';

    public const DELIVERY_STATUSES = [
        self::DELIVERY_PENDING,
        self::DELIVERY_PREPARING,
        self::DELIVERY_SHIPPED,
        self::DELIVERY_DELIVERED,
        self::DELIVERY_RETURNED,
    ];

    public const DELIVERY_TRANSITIONS = [
        self::DELIVERY_PENDING => [
            self::DELIVERY_PREPARING,
            self::DELIVERY_SHIPPED,
        ],
        self::DELIVERY_PREPARING => [
            self::DELIVERY_SHIPPED,
        ],
        self::DELIVERY_SHIPPED => [
            self::DELIVERY_DELIVERED,
            self::DELIVERY_RETURNED,
        ],
        self::DELIVERY_DELIVERED => [
            self::DELIVERY_RETURNED,
        ],
        self::DELIVERY_RETURNED => [],
    ];

    /* ---------------- Payment methods ---------------- */

    public const PAYMENT_COD = 'cod';
    public const PAYMENT_ONLINE = 'online';
    public const PAYMENT_BANK = 'bank_transfer';

    public const PAYMENT_METHODS = [
        self::PAYMENT_COD,
        self::PAYMENT_ONLINE,
        self::PAYMENT_BANK,
    ];

    /* ---------------- Payment gateways ---------------- */

    public const GATEWAY_MYFATOORAH = 'myfatoorah';
    public const GATEWAY_PAYMOB = 'paymob';
    public const GATEWAY_PAYPAL = 'paypal';

    public const PAYMENT_GATEWAYS = [
        self::GATEWAY_MYFATOORAH,
        self::GATEWAY_PAYMOB,
        self::GATEWAY_PAYPAL,
    ];

    /* ---------------- Payment status ---------------- */

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING,
        self::PAYMENT_PAID,
        self::PAYMENT_FAILED,
        self::PAYMENT_REFUNDED,
    ];

    /* ---------------- Blogger commission ---------------- */

    public const COMMISSION_PENDING = 'pending';
    public const COMMISSION_EARNED = 'earned';
    public const COMMISSION_CANCELLED = 'cancelled';

    public const COMMISSION_STATUSES = [
        self::COMMISSION_PENDING,
        self::COMMISSION_EARNED,
        self::COMMISSION_CANCELLED,
    ];

    /* ---------------- Badge colors ---------------- */

    public const BADGES = [
        'status' => [
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_CANCELLED => 'danger',
        ],

        'delivery_status' => [
            self::DELIVERY_PENDING => 'secondary',
            self::DELIVERY_PREPARING => 'warning',
            self::DELIVERY_SHIPPED => 'primary',
            self::DELIVERY_DELIVERED => 'success',
            self::DELIVERY_RETURNED => 'danger',
        ],

        'payment_status' => [
            self::PAYMENT_PENDING => 'warning',
            self::PAYMENT_PAID => 'success',
            self::PAYMENT_FAILED => 'danger',
            self::PAYMENT_REFUNDED => 'secondary',
        ],

        'commission_status' => [
            self::COMMISSION_PENDING => 'warning',
            self::COMMISSION_EARNED => 'success',
            self::COMMISSION_CANCELLED => 'danger',
        ],
    ];

    protected $fillable = [
        'order_number',
        'status',
        'customer_name',
        'customer_phone',

        'country_id',
        'city_id',
        'area_id',

        'shipping_address',
        'shipping_notes',
        'notes',

        'subtotal',
        'discount_total',
        'shipping_total',
        'total',

        'coupon_id',
        'coupon_code',

        'blogger_id',
        'commission_amount',
        'commission_status',

        'payment_method',
        'payment_gateway',
        'payment_status',
        'payment_id',
        'invoice_id',
        'invoice_url',
        'payment_data',
        'paid_at',

        'delivery_status',
        'tracking_number',
        'shipped_at',
        'delivered_at',
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

    /* ---------------- Relationships ---------------- */

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function blogger(): BelongsTo
    {
        return $this->belongsTo(Blogger::class);
    }

    /* ---------------- Helpers ---------------- */

    public function badge(string $column): string
    {
        return self::BADGES[$column][$this->{$column}] ?? 'secondary';
    }
}
