<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'clearance_signature_path')) {
                $table->string('clearance_signature_path')->nullable()->after('auto_approve');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'clearance_signature_path')) {
                $table->dropColumn('clearance_signature_path');
            }
        });
    }
};
