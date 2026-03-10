<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNurseClearanceToHealthProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('health_profiles', function (Blueprint $table) {
   
        $table->string('clearance_status')->nullable()->after('vaccine_history');
        $table->text('pending_reason')->nullable()->after('clearance_status');
        $table->timestamp('verified_at')->nullable()->after('pending_reason');
    });
}

public function down()
{
    Schema::table('health_profiles', function (Blueprint $table) {
        $table->dropColumn(['clearance_status', 'pending_reason', 'verified_at']);
    });
}
}
