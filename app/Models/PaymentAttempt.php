<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'idempotency_key',
        'payment_id',
        'invoice_id',
        'amount',
        'currency',
        'status',
        'response_data',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'response_data' => 'array',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
