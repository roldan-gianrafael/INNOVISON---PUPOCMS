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

            // --- PART I: STUDENT INFORMATION ---
            $table->string('school_year')->nullable();
            $table->string('home_address')->nullable();
            $table->string('student_photo')->nullable(); // File path ng 2x2 photo
            $table->integer('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('course_college')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('landline')->nullable();
            $table->string('cellphone')->nullable();

            // --- PART II: MEDICAL HISTORY ---
            $table->string('has_illness')->nullable(); // Yes/No
            $table->json('medical_history')->nullable(); // Checkboxes (Asthma, Heart Disease, etc.)
            $table->text('other_illness')->nullable();
            
            $table->string('has_disability')->nullable(); // None/Yes
            $table->string('disability_type')->nullable();

            // Section 3: Allergies
            $table->string('food_allergies')->nullable();
            $table->boolean('no_allergies')->default(false);
            $table->json('medicine_allergies')->nullable(); // Checkboxes (Aspirin, Penicillin, etc.)
            $table->string('other_med_allergies')->nullable();

            // --- PART III: SOCIAL & VACCINATION ---
            $table->string('is_smoker')->nullable(); 
            $table->string('is_drinker')->nullable();

            // COVID Vax Table (Naka-JSON para malinis ang table)
            // Dito papasok yung Date at Brand para sa 1st, 2nd, at Boosters
            $table->json('vaccine_history')->nullable();

            // Digital Signature
            $table->string('digital_signature')->nullable(); // File path ng signature image

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
        Schema::dropIfExists('health_profiles');
    }
}