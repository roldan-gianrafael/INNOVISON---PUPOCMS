<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'student_id')) {
                $table->string('student_id')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('health_profiles', 'student_number')) {
                $table->string('student_number')->nullable()->after('student_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('health_profiles', 'student_number')) {
                $table->dropColumn('student_number');
            }

            if (Schema::hasColumn('health_profiles', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });
    }
};
