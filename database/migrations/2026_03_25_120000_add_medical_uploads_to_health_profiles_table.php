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
            $table->string('pwd_id_proof')->nullable()->after('disability_type');
            $table->string('medical_certificate')->nullable()->after('other_med_allergies');
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'chest_xray_result',
                'pwd_id_proof',
                'medical_certificate',
            ]);
        });
    }
};
