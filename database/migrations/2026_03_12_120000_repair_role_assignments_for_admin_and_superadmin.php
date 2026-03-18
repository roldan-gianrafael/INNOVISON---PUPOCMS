<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize legacy assistant aliases to canonical "admin".
        DB::table('users')
            ->whereRaw('LOWER(user_role) IN (?, ?, ?)', ['student_assistant', 'studentassistant', 'assistant'])
            ->update([
                'user_role' => 'admin',
                'updated_at' => now(),
            ]);

        // Normalize legacy super admin alias to canonical "superadmin".
        DB::table('users')
            ->whereRaw('LOWER(user_role) = ?', ['super_admin'])
            ->update([
                'user_role' => 'superadmin',
                'updated_at' => now(),
            ]);

        // Repair accounts that were accidentally promoted by old role-merge migration.
        // Assistant profiles should stay on "admin", not "superadmin".
        if (Schema::hasColumn('users', 'user_type')) {
            DB::table('users')
                ->whereRaw('LOWER(user_role) = ?', ['superadmin'])
                ->whereRaw("LOWER(COALESCE(user_type, '')) = ?", ['assistant'])
                ->update([
                    'user_role' => 'admin',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible data repair.
    }
};

