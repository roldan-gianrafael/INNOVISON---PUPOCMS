<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdpRoleToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'idp_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('idp_role', 100)->nullable()->after('user_role');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'idp_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('idp_role');
            });
        }
    }
}
