<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The Firebase (FCM) token of each phone/browser of a user. One user can have several devices.
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Unique: a token belongs to ONE user. If another user logs in on the same phone,
            // the row is simply moved to him (see DeviceTokenController).
            $table->string('token', 512)->unique();

            $table->string('platform', 10);               // android | ios | web
            $table->string('locale', 5)->default('ar');   // the language of the push text (ar | en)
            $table->string('app_version', 20)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
