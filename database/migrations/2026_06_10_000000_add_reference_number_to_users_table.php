<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'reference_number')) {
                $table->string('reference_number')->nullable()->index()->after('student_number');
            }
        });

        if (Schema::hasTable('health_profiles') && Schema::hasColumn('health_profiles', 'reference_number')) {
            DB::table('users')
                ->join('health_profiles', 'health_profiles.user_id', '=', 'users.id')
                ->whereNull('users.reference_number')
                ->whereNotNull('health_profiles.reference_number')
                ->update([
                    'users.reference_number' => DB::raw('health_profiles.reference_number'),
                ]);
        }

        if (Schema::hasTable('pending_medical_assessments')) {
            DB::table('users')
                ->join('pending_medical_assessments', 'pending_medical_assessments.user_id', '=', 'users.id')
                ->whereNull('users.reference_number')
                ->whereNotNull('pending_medical_assessments.reference_number')
                ->update([
                    'users.reference_number' => DB::raw('pending_medical_assessments.reference_number'),
                ]);
        }

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'reference_number')) {
                $table->dropColumn('reference_number');
            }
        });
    }
};
