<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');

            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();

            $table->decimal('shipping_price', 8, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['city_id', 'name_ar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
