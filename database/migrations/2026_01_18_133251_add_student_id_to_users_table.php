<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function ($table) {
        $table->string('student_id')->unique()->nullable(); // do NOT use 'after' if unsure
    });
}

public function down()
{
    Schema::table('users', function ($table) {
        $table->dropColumn('student_id');
    });
}
}