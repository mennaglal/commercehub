<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_country')->nullable()->after('customer_phone');
            $table->string('shipping_city')->nullable()->after('shipping_country');
            $table->string('shipping_area')->nullable()->after('shipping_city');
            $table->text('shipping_notes')->nullable()->after('shipping_address');
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 30)->index();
            $table->string('old_value')->nullable();
            $table->string('new_value');
            $table->text('note')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_country',
                'shipping_city',
                'shipping_area',
                'shipping_notes',
            ]);
        });
    }
};
