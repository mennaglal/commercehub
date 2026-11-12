<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_return_id')->constrained('order_returns')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();

            $table->unsignedInteger('quantity');

            // Calculated by the server when the return is created (never taken from the form).
            $table->decimal('refund_amount', 12, 2);
            $table->decimal('commission_amount', 12, 2)->default(0);   // the blogger commission taken back

            // Chosen by the admin when the pieces arrive: true = the piece goes back to the stock.
            $table->boolean('restock')->default(false);

            $table->index('order_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_items');
    }
};
