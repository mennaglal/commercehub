<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('country_id')
                ->nullable()
                ->after('customer_phone')
                ->constrained('countries')
                ->nullOnDelete();

            $table->foreignId('city_id')
                ->nullable()
                ->after('country_id')
                ->constrained('cities')
                ->nullOnDelete();

            $table->foreignId('area_id')
                ->nullable()
                ->after('city_id')
                ->constrained('areas')
                ->nullOnDelete();

            $table->text('shipping_notes')
                ->nullable()
                ->after('shipping_address');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('country_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('area_id');

            $table->dropColumn('shipping_notes');
        });
    }
};
