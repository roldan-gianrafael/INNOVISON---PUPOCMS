<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'consultation_source')) {
                $table->string('consultation_source', 30)->nullable()->after('user_type');
            }

            if (!Schema::hasColumn('consultations', 'certificate_type')) {
                $table->string('certificate_type', 40)->nullable()->after('reason_for_visit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            foreach (['certificate_type', 'consultation_source'] as $column) {
                if (Schema::hasColumn('consultations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
