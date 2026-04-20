<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'blood_pressure')) {
                $table->string('blood_pressure')->nullable()->after('temperature');
            }

            if (!Schema::hasColumn('consultations', 'pulse_rate')) {
                $table->unsignedInteger('pulse_rate')->nullable()->after('blood_pressure');
            }

            if (!Schema::hasColumn('consultations', 'respiratory_rate')) {
                $table->unsignedInteger('respiratory_rate')->nullable()->after('pulse_rate');
            }

            if (!Schema::hasColumn('consultations', 'covid_status')) {
                $table->string('covid_status', 10)->nullable()->after('respiratory_rate');
            }

            if (!Schema::hasColumn('consultations', 'reason_for_visit')) {
                $table->string('reason_for_visit')->nullable()->after('covid_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            foreach (['reason_for_visit', 'covid_status', 'respiratory_rate', 'pulse_rate', 'blood_pressure'] as $column) {
                if (Schema::hasColumn('consultations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
