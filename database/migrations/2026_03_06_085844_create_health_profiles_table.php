<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('health_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Part 1: Additional Student Info
        $table->string('emergency_contact_name');
        $table->string('emergency_contact_number');
        $table->string('blood_type')->nullable();

        // Part 2: Medical History (Checkboxes saved as JSON)
        $table->json('medical_history')->nullable(); 

        // Part 3: Social History
        $table->string('is_smoker'); // Yes/No
        $table->string('is_drinker'); // Yes/No

        // Part 4: Vaccine History (JSON array para sa Date, Type, at Brand)
        $table->json('vaccine_history')->nullable();

        $table->timestamps();
    });
}
}
