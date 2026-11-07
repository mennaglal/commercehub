<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The history of the messages an admin sent to customers / vendors.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('body_ar');
            $table->text('body_en');
            $table->string('url', 500)->nullable();
            $table->string('audience', 20);                        // customers | vendors | all | user
            $table->unsignedBigInteger('user_id')->nullable();     // only for audience = user
            $table->boolean('with_push')->default(false);          // also sent to the phones (Firebase)
            $table->unsignedInteger('recipients_count')->default(0);   // filled when the sending job finishes
            $table->unsignedBigInteger('sent_by')->nullable();     // the admin (no foreign key)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_broadcasts');
    }
};
