<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // This item's share of the coupon discount and of the blogger commission.
            // Saved once when the order is created. A return uses them to refund exactly what was paid.
            $table->decimal('discount_amount', 12, 2)->default(0)->after('line_total');
            $table->decimal('commission_amount', 12, 2)->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['discount_amount', 'commission_amount']);
        });
    }
};
