<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Orders are kept even if the store is deleted.
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();

            // Order status: pending | confirmed | cancelled
            $table->string('status', 20)->default('pending')->index();

            // Customer
            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->text('shipping_address');
            $table->text('notes')->nullable();

            // Money
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            // Coupon (the code is copied, so the order stays readable if the coupon is deleted)
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('coupon_code', 50)->nullable();

            // Blogger commission, calculated once when the order is created
            $table->foreignId('blogger_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('commission_status', 20)->nullable()->index();   // pending | earned | cancelled

            // Payment
            $table->string('payment_method', 20)->default('cod');           // cod | online | bank_transfer
            $table->string('payment_gateway', 30)->nullable();              // fatura, tabby ... (online only)
            $table->string('payment_status', 20)->default('pending')->index(); // pending | paid | failed | refunded
            $table->string('payment_id')->nullable()->index();              // id on the gateway side
            $table->string('invoice_id')->nullable();
            $table->text('invoice_url')->nullable();
            $table->json('payment_data')->nullable();                       // raw gateway response / webhook
            $table->timestamp('paid_at')->nullable();

            // Delivery
            $table->string('delivery_status', 20)->default('pending')->index(); // pending | preparing | shipped | delivered | returned
            $table->string('tracking_number', 100)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
