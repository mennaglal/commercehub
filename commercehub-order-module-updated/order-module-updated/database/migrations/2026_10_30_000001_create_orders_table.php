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

            // Order status: pending, confirmed, cancelled.
            $table->string('status', 20)
                ->default('pending')
                ->index();

            // Customer information.
            $table->string('customer_name');
            $table->string('customer_phone', 30);
            $table->text('shipping_address');
            $table->text('notes')->nullable();

            // Order totals.
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('shipping_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            // Preserve coupon details if the coupon is deleted.
            $table->foreignId('coupon_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('coupon_code', 50)->nullable();

            // Blogger commission.
            $table->foreignId('blogger_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('commission_amount', 12, 2)->default(0);

            $table->string('commission_status', 20)
                ->nullable()
                ->index();

            // Payment method and selected gateway.
            $table->string('payment_method', 20)->default('cod');

            // myfatoorah, paymob, paypal.
            // Null for COD and bank transfer.
            $table->string('payment_gateway', 30)->nullable();

            // Overall payment status for this order.
            $table->string('payment_status', 20)
                ->default('pending')
                ->index();

            // Gateway-specific references.
            $table->string('payment_id')->nullable()->index();
            $table->string('invoice_id')->nullable();
            $table->text('invoice_url')->nullable();

            // Store only necessary, non-sensitive gateway metadata.
            $table->json('payment_data')->nullable();

            $table->timestamp('paid_at')->nullable();

            // Delivery information.
            $table->string('delivery_status', 20)
                ->default('pending')
                ->index();

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
