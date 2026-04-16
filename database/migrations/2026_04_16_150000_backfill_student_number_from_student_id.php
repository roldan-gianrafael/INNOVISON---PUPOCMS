<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'student_number') || !Schema::hasColumn('users', 'student_id')) {
            return;
        }

        DB::table('users')
            ->whereNull('student_number')
            ->whereNotNull('student_id')
            ->update([
                'student_number' => DB::raw('student_id'),
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'student_number') || !Schema::hasColumn('users', 'student_id')) {
            return;
        }

        DB::table('users')
            ->whereColumn('student_number', 'student_id')
            ->update([
                'student_number' => null,
            ]);
    }
};
