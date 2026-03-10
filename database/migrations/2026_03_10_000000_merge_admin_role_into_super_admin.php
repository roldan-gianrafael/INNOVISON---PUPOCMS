<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereRaw('LOWER(user_role) = ?', ['admin'])
            ->update([
                'user_role' => 'super_admin',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Intentionally irreversible: merged "admin" into "super_admin".
    }
};
