<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'student_number')) {
                $table->string('student_number')->nullable()->after('student_id');
            }
        });

        if (Schema::hasColumn('appointments', 'student_number') && Schema::hasTable('users')) {
            DB::table('appointments')
                ->join('users', 'users.id', '=', 'appointments.user_id')
                ->whereNull('appointments.student_number')
                ->whereNotNull('users.student_number')
                ->update([
                    'appointments.student_number' => DB::raw('users.student_number'),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'student_number')) {
                $table->dropColumn('student_number');
            }
        });
    }
};
