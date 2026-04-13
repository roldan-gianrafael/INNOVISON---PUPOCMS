<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admins') || Schema::hasColumn('admins', 'user_id')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('admin_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasColumn('admins', 'user_id')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
