<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHealthProfileStatusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function (Blueprint $col) {
        // Default is 0 (False), magiging 1 (True) pagkatapos mag-fill up
        $col->boolean('is_health_profile_completed')->default(0)->after('email');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $col) {
        $col->dropColumn('is_health_profile_completed');
    });
}
    }

