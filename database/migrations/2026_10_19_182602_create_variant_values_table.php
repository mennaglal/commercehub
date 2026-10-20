<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {


        Schema::create('variant_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_type_id')
                ->constrained('variant_types')
                ->cascadeOnDelete();

            $table->string('value_ar');
            $table->string('value_en');
            $table->string('color_code', 7)->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['variant_type_id', 'value_en'],
                'variant_values_type_value_unique'
            );
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('variant_values');
    }
};
