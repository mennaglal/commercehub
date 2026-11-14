<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');

            $table->foreignId('country_id')
                ->constrained('countries')
                ->restrictOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_id', 'name_ar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
