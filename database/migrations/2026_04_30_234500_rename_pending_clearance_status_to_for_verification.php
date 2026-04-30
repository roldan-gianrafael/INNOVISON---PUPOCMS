<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('health_profiles') || !Schema::hasColumn('health_profiles', 'clearance_status')) {
            return;
        }

        DB::table('health_profiles')
            ->where('clearance_status', 'Pending')
            ->update(['clearance_status' => 'For Verification']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('health_profiles') || !Schema::hasColumn('health_profiles', 'clearance_status')) {
            return;
        }

        DB::table('health_profiles')
            ->where('clearance_status', 'For Verification')
            ->update(['clearance_status' => 'Pending']);
    }
};
