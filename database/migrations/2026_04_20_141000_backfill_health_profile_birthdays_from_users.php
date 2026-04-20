<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('health_profiles')
            ->join('users', 'users.id', '=', 'health_profiles.user_id')
            ->whereNull('health_profiles.birthday')
            ->whereNotNull('users.DOB')
            ->update([
                'health_profiles.birthday' => DB::raw('users.DOB'),
            ]);
    }

    public function down(): void
    {
        DB::table('health_profiles')
            ->join('users', 'users.id', '=', 'health_profiles.user_id')
            ->whereColumn('health_profiles.birthday', 'users.DOB')
            ->update([
                'health_profiles.birthday' => null,
            ]);
    }
};
