<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizeSuperadminUserTypes extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'user_type')) {
            return;
        }

        DB::table('users')
            ->whereRaw('LOWER(user_role) IN (?, ?)', ['superadmin', 'super_admin'])
            ->update(['user_type' => 'Regular']);
    }

    public function down()
    {
        // The previous Assistant value was invalid for Super Admin accounts.
    }
}
