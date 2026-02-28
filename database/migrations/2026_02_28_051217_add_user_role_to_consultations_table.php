<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRoleToConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('consultations', function (Blueprint $table) {
        // Idadagdag ang user_role column, pwedeng null muna para sa mga lumang record
        $table->string('user_role')->nullable()->after('user_type'); 
    });
}

public function down()
{
    Schema::table('consultations', function (Blueprint $table) {
        $table->dropColumn('user_role');
    });
}
}
