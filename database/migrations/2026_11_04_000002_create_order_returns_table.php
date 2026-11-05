<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('return_number')->unique();

            // requested | approved | rejected | received | refunded
            $table->string('status', 20)->default('requested');

            $table->string('reason', 30);
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();

            // At request time: the maximum refund. At the refund step: what was really refunded.
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('refund_reference')->nullable();   // bank / gateway reference written by the admin

            // The admin who made the last change (no foreign key: it works with any admin table).
            $table->unsignedBigInteger('processed_by')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
