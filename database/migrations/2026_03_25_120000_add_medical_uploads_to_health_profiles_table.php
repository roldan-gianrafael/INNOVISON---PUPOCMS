<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->string('chest_xray_result')->nullable()->after('other_illness');
            $table->date('xray_date')->nullable()->after('chest_xray_result');
            $table->string('xray_findings')->nullable()->after('xray_date');
            $table->string('pwd_id_proof')->nullable()->after('disability_type');
            $table->string('medical_certificate')->nullable()->after('other_med_allergies');
            $table->string('doctor_name')->nullable()->after('medical_certificate');
            $table->date('med_cert_date')->nullable()->after('doctor_name');
            $table->string('med_cert_findings')->nullable()->after('med_cert_date');
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'chest_xray_result',
                'xray_date',
                'xray_findings',
                'pwd_id_proof',
                'medical_certificate',
                'doctor_name',
                'med_cert_date',
                'med_cert_findings',
            ]);
        });
    }
};
