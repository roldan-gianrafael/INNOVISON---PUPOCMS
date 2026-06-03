<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'medical_assessment_upload')) {
                $table->string('medical_assessment_upload')->nullable()->after('medical_certificate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('health_profiles', 'medical_assessment_upload')) {
                $table->dropColumn('medical_assessment_upload');
            }
        });
    }
};
