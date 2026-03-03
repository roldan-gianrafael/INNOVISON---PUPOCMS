<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('medical_conditions', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., Asthma, Hypertension, Peanut Allergy
        $table->foreignId('category_id')->constrained()->onDelete('cascade'); 
        $table->timestamps();   // Para sa created_at at updated_at
        $table->softDeletes();  // Para sa deleted_at field
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_conditions');
    }
}
