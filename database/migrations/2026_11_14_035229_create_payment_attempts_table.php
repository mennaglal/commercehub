<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('gateway', 50);

            $table->uuid('idempotency_key')->unique();

            $table->string('payment_id')->nullable();

            $table->string('invoice_id')->nullable();

            $table->decimal('amount', 15, 2);

            $table->string('currency', 3);

            $table->string('status', 30)->default('pending');

            $table->json('response_data')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['gateway', 'payment_id'],
                'payment_attempt_gateway_payment_unique'
            );

            $table->index(
                ['order_id', 'status'],
                'payment_attempt_order_status_index'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_attempts');
    }
};
