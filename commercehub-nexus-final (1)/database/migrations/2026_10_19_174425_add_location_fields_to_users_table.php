<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->foreignId('city_id')
                ->nullable()
                ->constrained('cities')
                ->nullOnDelete();

            $table->foreignId('area_id')
                ->nullable()
                ->constrained('areas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['area_id']);

            $table->dropColumn([
                'country_id',
                'city_id',
                'area_id',
            ]);
        });
    }
};
