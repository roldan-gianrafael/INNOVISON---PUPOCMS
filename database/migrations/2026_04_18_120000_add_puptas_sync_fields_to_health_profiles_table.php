<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'puptas_sync_status')) {
                $table->string('puptas_sync_status', 30)->nullable()->after('verified_at');
            }

            if (!Schema::hasColumn('health_profiles', 'puptas_synced_at')) {
                $table->timestamp('puptas_synced_at')->nullable()->after('puptas_sync_status');
            }

            if (!Schema::hasColumn('health_profiles', 'puptas_sync_message')) {
                $table->text('puptas_sync_message')->nullable()->after('puptas_synced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('health_profiles', 'puptas_sync_message') ? 'puptas_sync_message' : null,
                Schema::hasColumn('health_profiles', 'puptas_synced_at') ? 'puptas_synced_at' : null,
                Schema::hasColumn('health_profiles', 'puptas_sync_status') ? 'puptas_sync_status' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
