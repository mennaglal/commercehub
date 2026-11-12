<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->string('title_ar');
            $table->string('title_en');

            $table->string('slug')->unique();

            $table->longText('content_ar')->nullable();
            $table->longText('content_en')->nullable();

            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
