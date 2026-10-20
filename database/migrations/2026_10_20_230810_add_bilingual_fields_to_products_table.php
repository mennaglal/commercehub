<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('is_sale')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar',
                'name_en',
                'description_ar',
                'description_en',
                'sale_price',
                'is_sale',
            ]);
        });
    }
};
