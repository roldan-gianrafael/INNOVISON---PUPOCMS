<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        // Name Fields
        $table->string('student_id')->unique();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('name'); // Combined first & last
        
        // Identity & School Details
      
        $table->string('email')->unique();
        $table->date('DOB')->nullable();; 
        $table->string('course')->nullable();;
        $table->string('year')->nullable();;
        $table->string('section')->nullable();;
        
        // System Specifics (Dito yung request mo)
        $table->string('barcode')->nullable(); // Ang barcode data ng student
        $table->string('user_role')->default('student'); // e.g., super_admin, student, student_assistant
        $table->string('user_type')->nullable(); // e.g., nurse, doctor, regular_student
        
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
