<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();

            // The customer discount: fixed amount or percentage.
            $table->string('type', 20);                                   // fixed | percentage
            $table->decimal('value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();           // cap, used with percentage
            $table->decimal('min_order_amount', 10, 2)->nullable();       // minimum of the eligible products

            $table->unsignedInteger('usage_limit')->nullable();           // null = unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('status')->default(true);

            // null blogger = general coupon.
            $table->foreignId('blogger_id')->nullable()->constrained()->nullOnDelete();

            // The blogger's commission on each order that uses this coupon.
            $table->string('commission_type', 20)->nullable();            // fixed | percentage
            $table->decimal('commission_value', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
