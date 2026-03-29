<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admins') || Schema::hasColumn('admins', 'suffix_name')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->string('suffix_name')->nullable()->after('last_name');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('admins') || !Schema::hasColumn('admins', 'suffix_name')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('suffix_name');
        });
    }
};
