<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->string('height')->nullable()->after('school_year');
            $table->string('weight')->nullable()->after('height');
            $table->string('medical_certificate_issued_by')->nullable()->after('medical_certificate');
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'height',
                'weight',
                'medical_certificate_issued_by',
            ]);
        });
    }
};
