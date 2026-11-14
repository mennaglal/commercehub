<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_return_items', function (Blueprint $table) {
            $table->string('vendor_status', 20)->default('requested')->after('restock');
            $table->text('vendor_note')->nullable()->after('vendor_status');
            $table->timestamp('vendor_processed_at')->nullable()->after('vendor_note');
            $table->index(['vendor_status', 'order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::table('order_return_items', function (Blueprint $table) {
            $table->dropIndex(['vendor_status', 'order_item_id']);
            $table->dropColumn(['vendor_status', 'vendor_note', 'vendor_processed_at']);
        });
    }
};
