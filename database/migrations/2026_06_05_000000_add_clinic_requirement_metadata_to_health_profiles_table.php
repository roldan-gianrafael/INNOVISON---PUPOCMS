<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'doctor_name')) {
                $table->string('doctor_name')->nullable()->after('medical_certificate');
            }

            if (!Schema::hasColumn('health_profiles', 'med_cert_date')) {
                $table->date('med_cert_date')->nullable()->after('doctor_name');
            }

            if (!Schema::hasColumn('health_profiles', 'med_cert_findings')) {
                $table->string('med_cert_findings')->nullable()->after('med_cert_date');
            }

            if (!Schema::hasColumn('health_profiles', 'xray_date')) {
                $table->date('xray_date')->nullable()->after('chest_xray_result');
            }

            if (!Schema::hasColumn('health_profiles', 'xray_findings')) {
                $table->string('xray_findings')->nullable()->after('xray_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('health_profiles', 'xray_findings') ? 'xray_findings' : null,
                Schema::hasColumn('health_profiles', 'xray_date') ? 'xray_date' : null,
                Schema::hasColumn('health_profiles', 'med_cert_findings') ? 'med_cert_findings' : null,
                Schema::hasColumn('health_profiles', 'med_cert_date') ? 'med_cert_date' : null,
                Schema::hasColumn('health_profiles', 'doctor_name') ? 'doctor_name' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
