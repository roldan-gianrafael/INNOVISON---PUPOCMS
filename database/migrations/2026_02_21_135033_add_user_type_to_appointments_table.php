<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserTypeToAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('appointments', function (Blueprint $table) {
        // We use enum to match your existing XAMPP roles
        $table->enum('user_type', ['Student', 'Faculty', 'Admin', 'Dependent'])->nullable()->after('type');
    });
}

public function down()
{
    Schema::table('appointments', function (Blueprint $table) {
        $table->dropColumn('user_type');
    });
}
}
