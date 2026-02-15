<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function ($table) {
        // remove ->after('student_id')
        $table->string('barcode')->nullable();
    });
}

public function down()
{
    Schema::table('users', function ($table) {
        $table->dropColumn('barcode');
    });

}
}
