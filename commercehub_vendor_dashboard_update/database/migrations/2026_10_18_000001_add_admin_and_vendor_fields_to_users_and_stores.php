<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true)->after('avatar');
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['phone', 'avatar', 'status'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
