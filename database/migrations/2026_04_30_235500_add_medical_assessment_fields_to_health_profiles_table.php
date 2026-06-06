<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'assessment_date')) {
                $table->date('assessment_date')->nullable()->after('medical_certificate');
            }
            if (!Schema::hasColumn('health_profiles', 'blood_pressure')) {
                $table->string('blood_pressure', 30)->nullable()->after('weight');
            }
            if (!Schema::hasColumn('health_profiles', 'respiratory_rate')) {
                $table->string('respiratory_rate', 30)->nullable()->after('blood_pressure');
            }
            if (!Schema::hasColumn('health_profiles', 'temperature')) {
                $table->string('temperature', 30)->nullable()->after('respiratory_rate');
            }
            if (!Schema::hasColumn('health_profiles', 'covid_positive')) {
                $table->string('covid_positive', 10)->nullable()->after('temperature');
            }
            if (!Schema::hasColumn('health_profiles', 'medical_certificate_issued_by')) {
                $table->string('medical_certificate_issued_by')->nullable()->after('medical_certificate');
            }
            if (!Schema::hasColumn('health_profiles', 'medical_certificate_issued_at')) {
                $table->date('medical_certificate_issued_at')->nullable()->after('medical_certificate_issued_by');
            }
            if (!Schema::hasColumn('health_profiles', 'chest_xray_result_text')) {
                $table->string('chest_xray_result_text')->nullable()->after('chest_xray_result');
            }
            if (!Schema::hasColumn('health_profiles', 'chest_xray_date')) {
                $table->date('chest_xray_date')->nullable()->after('chest_xray_result_text');
            }
            if (!Schema::hasColumn('health_profiles', 'assessment_remarks')) {
                $table->text('assessment_remarks')->nullable()->after('chest_xray_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            foreach ([
                'assessment_date',
                'blood_pressure',
                'respiratory_rate',
                'temperature',
                'covid_positive',
                'medical_certificate_issued_by',
                'medical_certificate_issued_at',
                'chest_xray_result_text',
                'chest_xray_date',
                'assessment_remarks',
            ] as $column) {
                if (Schema::hasColumn('health_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

