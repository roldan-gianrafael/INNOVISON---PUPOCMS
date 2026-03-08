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
        // Ang status kung Issued o Pending
        $table->string('clearance_status')->nullable()->after('vaccine_history');
        
        // Dito ise-save ang path ng signature ni nurse
        $table->string('physician_signature')->nullable()->after('clearance_status');
        
        // Reason kung bakit Pending ang status
        $table->text('pending_reason')->nullable()->after('physician_signature');
        
        // Date kung kailan na-verify ni nurse
        $table->timestamp('verified_at')->nullable()->after('pending_reason');
    });
}

public function down()
{
    Schema::table('health_profiles', function (Blueprint $table) {
        $table->dropColumn(['clearance_status', 'physician_signature', 'pending_reason', 'verified_at']);
    });
}
}
